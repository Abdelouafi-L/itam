<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\License;
use App\Models\Maintenance;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | RF-25 — Asset Inventory Report
    |--------------------------------------------------------------------------
    */

    /**
     * Show the asset inventory report — filterable HTML view.
     */
    public function inventory(Request $request)
    {
        $query = Product::with([
            'category',
            'hardware',
            'software',
            'stock',
        ]);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type === 'hardware') {
                $query->whereHas('hardware');
            } elseif ($request->type === 'software') {
                $query->whereHas('software');
            }
        }

        // Filter by stock availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->whereHas('stock', fn($q) =>
                    $q->where('quantity_available', '>', 0)
                );
            } elseif ($request->availability === 'out') {
                $query->whereHas('stock', fn($q) =>
                    $q->where('quantity_available', 0)
                );
            }
        }

        $products  = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        // Summary stats for the report header
        $summary = [
            'total'     => $products->count(),
            'available' => $products->sum(fn($p) =>
                               $p->stock?->quantity_available ?? 0),
            'assigned'  => $products->sum(fn($p) =>
                               $p->stock?->quantity_assigned ?? 0),
        ];

        return view('reports.inventory',
                    compact('products', 'categories', 'summary'));
    }

    /**
     * Export asset inventory as PDF — RF-28.
     */
    public function inventoryPdf(Request $request)
    {
        $products = Product::with([
            'category', 'hardware', 'software', 'stock'
        ])->orderBy('name')->get();

        $summary = [
            'total'     => $products->count(),
            'available' => $products->sum(fn($p) =>
                            $p->stock?->quantity_available ?? 0),
            'assigned'  => $products->sum(fn($p) =>
                            $p->stock?->quantity_assigned ?? 0),
        ];

        $pdf = Pdf::loadView('reports.pdf.inventory',
                            compact('products', 'summary'))
                ->setPaper('a4', 'landscape');

        return $pdf->stream('inventaire-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export asset inventory as CSV — RF-28.
     */
    public function inventoryCsv(Request $request)
    {
        $products = Product::with([
            'category', 'hardware', 'software', 'stock'
        ])->orderBy('name')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventaire-'
                                     . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID', 'Nom', 'Catégorie', 'Type', 'Marque',
                'Modèle', 'Total', 'Disponible', 'Affecté'
            ]);

            // Data rows
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->category->name ?? '—',
                    $product->hardware ? 'Hardware' : 'Software',
                    $product->brand ?? '—',
                    $product->model ?? '—',
                    $product->stock?->quantity_total ?? 0,
                    $product->stock?->quantity_available ?? 0,
                    $product->stock?->quantity_assigned ?? 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | RF-26 — License Compliance Report
    |--------------------------------------------------------------------------
    */

    /**
     * Show the license compliance report.
     */
    public function licenses(Request $request)
    {
        $query = License::with(['software.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by expiring soon
        if ($request->filled('expiring')) {
            $query->whereNotNull('expiry_date')
                  ->whereDate('expiry_date', '>=', now())
                  ->whereDate('expiry_date', '<=',
                              now()->addDays(30));
        }

        $licenses = $query->orderBy('expiry_date')->get();

        $summary = [
            'total'    => $licenses->count(),
            'active'   => $licenses->where('status', 'Active')->count(),
            'expiring' => $licenses->filter(fn($l) =>
                              $l->isExpiringSoon())->count(),
            'expired'  => $licenses->where('status', 'Expirée')->count(),
        ];

        return view('reports.licenses',
                    compact('licenses', 'summary'));
    }

    /**
     * Export license report as PDF — RF-28.
     */
    public function licensesPdf(Request $request)
    {
        $licenses = License::with(['software.product'])
                           ->orderBy('expiry_date')
                           ->get();

        $summary = [
            'total'    => $licenses->count(),
            'active'   => $licenses->where('status', 'Active')->count(),
            'expiring' => $licenses->filter(fn($l) =>
                              $l->isExpiringSoon())->count(),
            'expired'  => $licenses->where('status', 'Expirée')->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.licenses',
                             compact('licenses', 'summary'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('licences-' . now()->format('Y-m-d') . '.pdf');
    
    }

    /**
     * Export license report as CSV — RF-28.
     */
    public function licensesCsv(Request $request)
    {
        $licenses = License::with(['software.product'])
                           ->orderBy('expiry_date')
                           ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="licences-'
                                     . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($licenses) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Logiciel', 'Version', 'Statut', 'Sièges Total',
                'Sièges Utilisés', 'Disponibles',
                'Date Achat', 'Date Expiration', 'Jours Restants',
                'Coût (MAD)'
            ]);

            foreach ($licenses as $license) {
                fputcsv($file, [
                    $license->software->product->name ?? '—',
                    $license->software->version ?? '—',
                    $license->status,
                    $license->seats_total,
                    $license->seats_used,
                    $license->seats_available,
                    $license->purchase_date?->format('d/m/Y') ?? '—',
                    $license->expiry_date?->format('d/m/Y') ?? '—',
                    $license->days_remaining ?? '—',
                    $license->cost ?? '0',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | RF-27 — Maintenance Cost Report
    |--------------------------------------------------------------------------
    */

    /**
     * Show the maintenance cost report.
     */
    public function maintenances(Request $request)
    {
        $query = Maintenance::with([
            'hardware.product',
            'technician.employee',
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $maintenances = $query->orderByDesc('date')->get();

        $summary = [
            'total'      => $maintenances->count(),
            'total_cost' => $maintenances->sum('cost'),
            'completed'  => $maintenances->where('status',
                                'Terminée')->count(),
            'in_progress'=> $maintenances->where('status',
                                'En cours')->count(),
        ];

        return view('reports.maintenances',
                    compact('maintenances', 'summary'));
    }

    /**
     * Export maintenance report as PDF — RF-28.
     */
    public function maintenancesPdf(Request $request)
    {
        $maintenances = Maintenance::with([
            'hardware.product',
            'technician.employee',
        ])->orderByDesc('date')->get();

        $summary = [
            'total'      => $maintenances->count(),
            'total_cost' => $maintenances->sum('cost'),
            'completed'  => $maintenances->where('status',
                                'Terminée')->count(),
            'in_progress'=> $maintenances->where('status',
                                'En cours')->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.maintenances',
                             compact('maintenances', 'summary'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('maintenances-' . now()->format('Y-m-d') . '.pdf');
    
    }

    /**
     * Export maintenance report as CSV — RF-28.
     */
    public function maintenancesCsv(Request $request)
    {
        $maintenances = Maintenance::with([
            'hardware.product',
            'technician.employee',
        ])->orderByDesc('date')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="maintenances-'
                                     . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($maintenances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Équipement', 'Type', 'Technicien',
                'Date', 'Statut', 'Coût (MAD)', 'Description'
            ]);

            foreach ($maintenances as $m) {
                fputcsv($file, [
                    $m->hardware->product->name ?? '—',
                    $m->type,
                    $m->technician->full_name ?? '—',
                    $m->date->format('d/m/Y'),
                    $m->status,
                    $m->cost ?? '0',
                    $m->description ?? '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}