<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentDetail;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     * RF-15: Full history for Admin/Tech/Manager.
     * RF-16: Employee sees own assignments only.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // RF-16 — Employee sees only their own assignments
        if ($user->hasRole('Employé')) {
            $assignments = Assignment::with([
                'employee', 'details.product', 'createdBy'
            ])
            ->where('employee_id', $user->employee_id)
            ->orderByDesc('assigned_at')
            ->paginate(15);
        } else {
            $assignments = Assignment::with([
                'employee', 'details.product', 'createdBy'
            ])
            ->orderByDesc('assigned_at')
            ->paginate(15);
        }

        return view('assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment.
     * RF-12: Assign asset to employee.
     */
    public function create()
    {
        $employees = Employee::active()
                            ->with('department')
                            ->orderBy('last_name')
                            ->get();

        $products = Product::with(['stock', 'category',
                                'hardware', 'software'])
                        ->whereHas('stock', function ($q) {
                            $q->where('quantity_available', '>', 0);
                        })
                        ->orderBy('name')
                        ->get();

        // Pre-format products for JavaScript — avoids Blade/JS conflicts
        $productsJson = $products->map(fn($p) => [
            'id'        => $p->id,
            'name'      => $p->name,
            'brand'     => $p->brand ?? '',
            'available' => $p->stock?->quantity_available ?? 0,
            'type'      => $p->hardware ? 'hardware' : 'software',
        ])->toJson();

        return view('assignments.create',
                    compact('employees', 'products', 'productsJson'));
    }

    /**
     * Store a newly created assignment.
     * RF-12 + RF-13: Verify availability before assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'          => ['required', 'exists:employees,id'],
            'notes'                => ['nullable', 'string', 'max:1000'],
            'products'             => ['required', 'array', 'min:1'],
            'products.*.id'        => ['required', 'exists:products,id'],
            'products.*.quantity'  => ['required', 'integer', 'min:1'],
            'products.*.serial_number' => ['nullable', 'string',
                                           'max:100'],
            'products.*.asset_tag' => ['nullable', 'string', 'max:100'],
            'products.*.notes'     => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request) {

            // Create assignment header
            $assignment = Assignment::create([
                'employee_id' => $request->employee_id,
                'created_by'  => Auth::id(),
                'assigned_at' => now(),
                'status'      => 'Active',
                'notes'       => $request->notes,
            ]);

            // Process each product line
            foreach ($request->products as $line) {
                $product = Product::findOrFail($line['id']);
                $stock   = $product->stock;

                // RF-13 — Verify availability
                if (!$stock || $stock->quantity_available < $line['quantity']) {
                    throw new \Exception(
                        "Stock insuffisant pour: {$product->name}"
                    );
                }

                // Create detail line
                AssignmentDetail::create([
                    'assignment_id' => $assignment->id,
                    'product_id'    => $product->id,
                    'quantity'      => $line['quantity'],
                    'returned_qty'  => 0,
                    'serial_number' => $line['serial_number'] ?? null,
                    'asset_tag'     => $line['asset_tag'] ?? null,
                    'notes'         => $line['notes'] ?? null,
                ]);

                // Update stock — decrease available, increase assigned
                $stock->update([
                    'quantity_available' => $stock->quantity_available
                                           - $line['quantity'],
                    'quantity_assigned'  => $stock->quantity_assigned
                                           + $line['quantity'],
                    'updated_at'         => now(),
                ]);
            }
        });

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Affectation créée avec succès.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load([
            'employee.department',
            'details.product.category',
            'details.product.hardware',
            'details.product.software',
            'createdBy.employee',
        ]);

        return view('assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing — not used.
     * Assignments are immutable — only returns are allowed.
     */
    public function edit(Assignment $assignment)
    {
        return redirect()->route('assignments.show', $assignment);
    }

    /**
     * Update not used directly.
     * Use returnAsset() instead.
     */
    public function update(Request $request, Assignment $assignment)
    {
        return redirect()->route('assignments.show', $assignment);
    }

    /**
     * Handle asset return — RF-14.
     * Records return, updates stock, closes assignment if fully returned.
     */
    public function returnAsset(Request $request, Assignment $assignment)
    {
        $request->validate([
            'details'                => ['required', 'array'],
            'details.*.returned_qty' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $assignment) {

            $allReturned = true;

            foreach ($request->details as $detailId => $data) {
                $detail = AssignmentDetail::findOrFail($detailId);
                $returnQty = (int) $data['returned_qty'];

                // Cannot return more than what was assigned
                $maxReturn = $detail->quantity - $detail->returned_qty;
                $returnQty = min($returnQty, $maxReturn);

                if ($returnQty > 0) {
                    // Update detail returned quantity
                    $detail->update([
                        'returned_qty' => $detail->returned_qty
                                          + $returnQty,
                    ]);

                    // Update stock — increase available, decrease assigned
                    $stock = $detail->product->stock;
                    $stock->update([
                        'quantity_available' => $stock->quantity_available
                                               + $returnQty,
                        'quantity_assigned'  => $stock->quantity_assigned
                                               - $returnQty,
                        'updated_at'         => now(),
                    ]);
                }

                // Check if this detail is fully returned
                $detail->refresh();
                if (!$detail->isFullyReturned()) {
                    $allReturned = false;
                }
            }

            // RF-14 — If all items returned, close the assignment
            if ($allReturned) {
                $assignment->update([
                    'status'      => 'Clôturée',
                    'returned_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('assignments.show', $assignment)
            ->with('success', 'Retour enregistré avec succès.');
    }

    /**
     * Delete — not allowed for assignments.
     * Assignments are permanent audit records.
     */
    public function destroy(Assignment $assignment)
    {
        return redirect()
            ->route('assignments.index')
            ->with('error',
                'Les affectations ne peuvent pas être supprimées —
                 elles font partie de l\'historique d\'audit.'
            );
    }
}