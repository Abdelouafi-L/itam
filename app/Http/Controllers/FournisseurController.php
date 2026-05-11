<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    /**
     * RF-31: View supplier list — filterable.
     */
    public function index(Request $request)
    {
        $query = Fournisseur::withCount('livraisons');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_nom', 'like', "%{$search}%");
            });
        }

        $fournisseurs = $query->orderBy('nom')->paginate(15)
                              ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    /**
     * RF-29: Show create form.
     */
    public function create()
    {
        return view('fournisseurs.create');
    }

    /**
     * RF-29: Store new supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'         => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'adresse'     => ['nullable', 'string', 'max:500'],
            'contact_nom' => ['nullable', 'string', 'max:100'],
            'site_web'    => ['nullable', 'url', 'max:255'],
            'numero_tva'  => ['nullable', 'string', 'max:50'],
        ]);

        Fournisseur::create($validated);

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur ajouté avec succès.');
    }

    /**
     * RF-32: View supplier with delivery history.
     */
    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load([
            'livraisons.details.product',
            'livraisons.signataire',
        ]);

        return view('fournisseurs.show', compact('fournisseur'));
    }

    /**
     * RF-30: Show edit form.
     */
    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    /**
     * RF-30: Update supplier.
     */
    public function update(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'nom'         => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'adresse'     => ['nullable', 'string', 'max:500'],
            'contact_nom' => ['nullable', 'string', 'max:100'],
            'site_web'    => ['nullable', 'url', 'max:255'],
            'numero_tva'  => ['nullable', 'string', 'max:50'],
        ]);

        $fournisseur->update($validated);

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Delete supplier — only if no deliveries.
     */
    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->livraisons()->count() > 0) {
            return redirect()
                ->route('fournisseurs.index')
                ->with('error',
                    'Impossible de supprimer ce fournisseur — ' .
                    'il est lié à des livraisons existantes.'
                );
        }

        $fournisseur->delete();

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}