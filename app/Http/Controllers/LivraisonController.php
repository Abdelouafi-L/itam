<?php

namespace App\Http\Controllers;

use App\Models\DetailLivraison;
use App\Models\Employee;
use App\Models\Fournisseur;
use App\Models\Livraison;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LivraisonController extends Controller
{
    /**
     * RF-39: View delivery history — filterable.
     */
    public function index(Request $request)
    {
        $query = Livraison::with([
            'fournisseur',
            'signataire',
            'details',
        ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_livraison', '>=',
                              $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_livraison', '<=',
                              $request->date_to);
        }

        $livraisons   = $query->orderByDesc('date_livraison')
                              ->paginate(15)
                              ->withQueryString();
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('livraisons.index',
                    compact('livraisons', 'fournisseurs'));
    }

    /**
     * RF-33: Show create form.
     */
    public function create()
    {
        $fournisseurs    = Fournisseur::orderBy('nom')->get();
        $employees       = Employee::where('is_active', true)
                                ->orderBy('last_name')
                                ->get();
        $products        = Product::with(['stock', 'category'])
                            ->orderBy('name')
                            ->get();

        $productsJson = $products->map(fn($p) => [
            'id'    => $p->id,
            'name'  => $p->name,
            'brand' => $p->brand ?? '',
        ])->toJson();

        $reference = Livraison::generateReference();

        // Pre-select supplier if coming from supplier page
        $selectedFournisseurId = request('fournisseur_id');

        return view('livraisons.create',
                    compact('fournisseurs', 'employees',
                            'products', 'productsJson',
                            'reference', 'selectedFournisseurId'));
    }

    /**
     * RF-33 + RF-34: Store new delivery with detail lines.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'          => ['required', 'exists:fournisseurs,id'],
            'signataire_id'           => ['required', 'exists:employees,id'],
            'bon_de_livraison'        => ['required', 'string', 'max:100'],
            'date_livraison'          => ['required', 'date'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
            'products'                => ['required', 'array', 'min:1'],
            'products.*.id'           => ['required', 'exists:products,id'],
            'products.*.quantite'     => ['required', 'integer', 'min:1'],
            'products.*.prix_unitaire'=> ['nullable', 'numeric', 'min:0'],
            'products.*.notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $livraison = null;

        DB::transaction(function () use ($request, &$livraison) {

            // Always generate reference server-side — never trust user input
            $livraison = Livraison::create([
                'fournisseur_id'    => $request->fournisseur_id,
                'signataire_id'     => $request->signataire_id,
                'reference_interne' => Livraison::generateReference(),
                'bon_de_livraison'  => $request->bon_de_livraison,
                'date_livraison'    => $request->date_livraison,
                'statut'            => 'En attente',
                'notes'             => $request->notes,
                'created_at'        => now(),
            ]);

            foreach ($request->products as $line) {
                DetailLivraison::create([
                    'livraison_id'  => $livraison->id,
                    'product_id'    => $line['id'],
                    'quantite'      => $line['quantite'],
                    'prix_unitaire' => $line['prix_unitaire'] ?? null,
                    'notes'         => $line['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('livraisons.show', $livraison)
            ->with('success', 'Livraison créée avec succès. En attente de réception.');
    }

    /**
     * Show delivery details with lifecycle actions.
     */
    public function show(Livraison $livraison)
    {
        $livraison->load([
            'fournisseur',
            'signataire',
            'details.product.stock',
            'details.product.category',
        ]);

        return view('livraisons.show', compact('livraison'));
    }

    /**
     * Edit — only En attente deliveries can be edited.
     */
    public function edit(Livraison $livraison)
    {
        if (!$livraison->isEnAttente()) {
            return redirect()
                ->route('livraisons.show', $livraison)
                ->with('error',
                    'Seules les livraisons "En attente" ' .
                    'peuvent être modifiées.'
                );
        }

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $employees    = Employee::where('is_active', true)
                                ->orderBy('last_name')->get();

        return view('livraisons.edit',
                    compact('livraison', 'fournisseurs', 'employees'));
    }

    /**
     * Update delivery header info.
     */
    public function update(Request $request, Livraison $livraison)
    {
        if (!$livraison->isEnAttente()) {
            return redirect()
                ->route('livraisons.show', $livraison)
                ->with('error', 'Cette livraison ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'fournisseur_id'   => ['required', 'exists:fournisseurs,id'],
            'signataire_id'    => ['required', 'exists:employees,id'],
            'bon_de_livraison' => ['required', 'string', 'max:100'],
            'date_livraison'   => ['required', 'date'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $livraison->update($validated);

        return redirect()
            ->route('livraisons.show', $livraison)
            ->with('success', 'Livraison mise à jour avec succès.');
    }

    /**
     * RF-35: Validate full reception — updates stock automatically.
     * MANUAL trigger by Technicien.
     */
    public function receptionner(Livraison $livraison)
    {
        if ($livraison->isClosed()) {
            return redirect()
                ->route('livraisons.show', $livraison)
                ->with('error', 'Cette livraison est déjà clôturée.');
        }

        DB::transaction(function () use ($livraison) {

            // Update stock for ALL detail lines
            foreach ($livraison->details as $detail) {
                $stock = $detail->product->stock;

                if ($stock) {
                    $stock->update([
                        'quantity_total'     => $stock->quantity_total
                                               + $detail->quantite,
                        'quantity_available' => $stock->quantity_available
                                               + $detail->quantite,
                        'updated_at'         => now(),
                    ]);
                } else {
                    // Create stock if it doesn't exist
                    Stock::create([
                        'product_id'         => $detail->product_id,
                        'quantity_total'     => $detail->quantite,
                        'quantity_available' => $detail->quantite,
                        'quantity_assigned'  => 0,
                    ]);
                }
            }

            // Close the delivery
            $livraison->update(['statut' => 'Réceptionnée']);
        });

        return redirect()
            ->route('livraisons.show', $livraison)
            ->with('success',
                'Livraison réceptionnée. ' .
                'Le stock a été mis à jour automatiquement.'
            );
    }

    /**
     * RF-36: Mark as Partielle — partial stock update.
     */
    public function partielle(Request $request, Livraison $livraison)
    {
        if ($livraison->isClosed()) {
            return redirect()
                ->route('livraisons.show', $livraison)
                ->with('error', 'Cette livraison est déjà clôturée.');
        }

        $request->validate([
            'details'            => ['required', 'array'],
            'details.*.quantite' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $livraison) {

            foreach ($request->details as $detailId => $data) {
                $detail  = DetailLivraison::findOrFail($detailId);
                $qty     = (int) $data['quantite'];

                if ($qty <= 0) continue;

                $stock = $detail->product->stock;

                if ($stock) {
                    $stock->update([
                        'quantity_total'     => $stock->quantity_total + $qty,
                        'quantity_available' => $stock->quantity_available + $qty,
                        'updated_at'         => now(),
                    ]);
                }
            }

            $livraison->update(['statut' => 'Partielle']);
        });

        return redirect()
            ->route('livraisons.show', $livraison)
            ->with('success',
                'Réception partielle enregistrée. ' .
                'Stock mis à jour pour les lignes validées.'
            );
    }

    /**
     * RF-37: Cancel delivery — no stock update.
     */
    public function annuler(Livraison $livraison)
    {
        if ($livraison->isClosed()) {
            return redirect()
                ->route('livraisons.show', $livraison)
                ->with('error', 'Cette livraison est déjà clôturée.');
        }

        $livraison->update(['statut' => 'Annulée']);

        return redirect()
            ->route('livraisons.show', $livraison)
            ->with('success',
                'Livraison annulée. Aucune mise à jour du stock.'
            );
    }

    /**
     * Destroy — only En attente can be deleted.
     */
    public function destroy(Livraison $livraison)
    {
        if (!$livraison->isEnAttente()) {
            return redirect()
                ->route('livraisons.index')
                ->with('error',
                    'Seules les livraisons "En attente" ' .
                    'peuvent être supprimées.'
                );
        }

        $livraison->delete();

        return redirect()
            ->route('livraisons.index')
            ->with('success', 'Livraison supprimée avec succès.');
    }
}