<?php

namespace App\Http\Controllers;

use App\Models\Hardware;
use App\Models\Maintenance;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display maintenance history.
     * RF-22: Full history with costs.
     */
    public function index()
    {
        $maintenances = Maintenance::with([
            'hardware.product',
            'technician.employee',
        ])
        ->orderByDesc('date')
        ->paginate(15);

        // Total cost for all maintenances
        $totalCost = Maintenance::sum('cost');

        return view('maintenances.index',
                    compact('maintenances', 'totalCost'));
    }

    /**
     * Show the form for creating a maintenance record.
     * RF-21: Create maintenance record.
     */
    public function create()
    {
        // Only hardware products can be maintained
        $hardwares = Hardware::with('product')
                             ->whereHas('product', function ($q) {
                                 $q->whereNotNull('id');
                             })
                             ->get();

        // Only technicians and admins can be assigned
        $technicians = User::role(['Administrateur', 'Technicien'])
                        ->where('is_active', true)
                        ->with('employee')
                        ->get();

        return view('maintenances.create',
                    compact('hardwares', 'technicians'));
    }

    /**
     * Store a new maintenance record.
     * RF-21: Auto-updates hardware condition.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hardware_id'    => ['required', 'exists:hardware,id'],
            'technician_id'  => ['required', 'exists:users,id'],
            'type'           => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:1000'],
            'date'           => ['required', 'date'],
            'cost'           => ['nullable', 'numeric', 'min:0'],
            'status'         => ['required',
                                 'in:Planifiée,En cours,Terminée'],
            'condition'      => ['nullable', 'string',
                                 'in:Neuf,Bon,Usagé,Endommagé'],
        ]);

        DB::transaction(function () use ($validated) {

            // Create maintenance record
            Maintenance::create([
                'hardware_id'   => $validated['hardware_id'],
                'technician_id' => $validated['technician_id'],
                'type'          => $validated['type'],
                'description'   => $validated['description'] ?? null,
                'date'          => $validated['date'],
                'cost'          => $validated['cost'] ?? null,
                'status'        => $validated['status'],
            ]);

            // RF-21 — Update hardware condition if provided
            if (!empty($validated['condition'])) {
                Hardware::find($validated['hardware_id'])
                    ->update(['condition' => $validated['condition']]);
            }
        });

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'Maintenance enregistrée avec succès.');
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load([
            'hardware.product.category',
            'technician.employee',
        ]);

        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * Show the form for editing a maintenance record.
     */
    public function edit(Maintenance $maintenance)
    {
        $maintenance->load('hardware.product');

        $technicians = User::role(['Administrateur', 'Technicien'])
                        ->where('is_active', true)
                        ->with('employee')
                        ->get();

        return view('maintenances.edit',
                    compact('maintenance', 'technicians'));
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:users,id'],
            'type'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'date'          => ['required', 'date'],
            'cost'          => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required',
                                'in:Planifiée,En cours,Terminée'],
            'condition'     => ['nullable', 'string',
                                'in:Neuf,Bon,Usagé,Endommagé'],
        ]);

        DB::transaction(function () use ($validated, $maintenance) {

            $maintenance->update([
                'technician_id' => $validated['technician_id'],
                'type'          => $validated['type'],
                'description'   => $validated['description'] ?? null,
                'date'          => $validated['date'],
                'cost'          => $validated['cost'] ?? null,
                'status'        => $validated['status'],
            ]);

            // Update hardware condition if provided
            if (!empty($validated['condition'])) {
                $maintenance->hardware
                    ->update(['condition' => $validated['condition']]);
            }
        });

        return redirect()
            ->route('maintenances.show', $maintenance)
            ->with('success', 'Maintenance mise à jour avec succès.');
    }

    /**
     * Delete a maintenance record.
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()
            ->route('maintenances.index')
            ->with('success', 'Maintenance supprimée avec succès.');
    }

    /**
     * Retire a hardware asset — RF-23.
     * Irreversible — Admin only — requires confirmation.
     */
    public function retire(Request $request, Hardware $hardware)
    {
        $request->validate([
            'confirmation' => ['required', 'in:RETIRER'],
        ]);

        // Set condition to Endommagé and mark as retired
        // We use condition as the retirement indicator
        $hardware->update([
            'condition' => 'Retiré',
        ]);

        // Close all open maintenance records for this hardware
        $hardware->maintenances()->where('status', '!=', 'Terminée')
                ->update(['status' => 'Terminée']);

        // Zero out stock — retired hardware is no longer available
        $stock = $hardware->product->stock;
        if ($stock) {
            $newTotal     = max(0, $stock->quantity_total - 1);
            $newAvailable = $stock->quantity_available;
            $newAssigned  = $stock->quantity_assigned;

            if ($request->boolean('is_assigned')) {
                // Unit was assigned — decrement assigned
                $newAssigned  = max(0, $stock->quantity_assigned - 1);
            } else {
                // Unit was available — decrement available
                $newAvailable = max(0, $stock->quantity_available - 1);
            }

            $stock->update([
                'quantity_total'     => $newTotal,
                'quantity_available' => $newAvailable,
                'quantity_assigned'  => $newAssigned,
            ]);
        }

        return redirect()
            ->route('maintenances.index')
            ->with('success',
                'Équipement retiré définitivement du parc. ' .
                'Cette action est irréversible.'
            );
    }
}