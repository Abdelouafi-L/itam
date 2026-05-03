<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Software;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Display a listing of all licenses.
     * RF-19: View license status with days remaining.
     */
    public function index()
    {
        $licenses = License::with(['software.product'])
                           ->orderBy('expiry_date')
                           ->get();

        return view('licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new license.
     * Only Software products without an existing license are shown.
     */
    public function create()
    {
        // Only show software products that don't have a license yet
        $softwares = Software::with('product')
                             ->whereDoesntHave('license')
                             ->get();

        return view('licenses.create', compact('softwares'));
    }

    /**
     * Store a newly created license.
     * RF-17: Add software license with full details.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'software_id'   => ['required', 'exists:software,id',
                                'unique:licenses,software_id'],
            'seats_total'   => ['required', 'integer', 'min:1'],
            'purchase_date' => ['nullable', 'date'],
            'expiry_date'   => ['nullable', 'date',
                                'after_or_equal:purchase_date'],
            'cost'          => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required',
                                'in:Active,Expirée,Résiliée'],
        ]);

        // seats_used starts at 0 on creation
        $validated['seats_used'] = 0;

        License::create($validated);

        return redirect()
            ->route('licenses.index')
            ->with('success', 'Licence ajoutée avec succès.');
    }

    /**
     * Display the specified license.
     * RF-19: Shows days remaining until expiry.
     */
    public function show(License $license)
    {
        $license->load('software.product');

        return view('licenses.show', compact('license'));
    }

    /**
     * Show the form for editing the specified license.
     */
    public function edit(License $license)
    {
        $license->load('software.product');

        return view('licenses.edit', compact('license'));
    }

    /**
     * Update the specified license.
     * RF-18: Track seats used vs total.
     */
    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'seats_total'   => ['required', 'integer', 'min:1'],
            'seats_used'    => ['required', 'integer', 'min:0',
                                'lte:seats_total'],
            'purchase_date' => ['nullable', 'date'],
            'expiry_date'   => ['nullable', 'date',
                                'after_or_equal:purchase_date'],
            'cost'          => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required',
                                'in:Active,Expirée,Résiliée'],
        ]);

        $license->update($validated);

        return redirect()
            ->route('licenses.index')
            ->with('success', 'Licence mise à jour avec succès.');
    }

    /**
     * Remove the specified license.
     */
    public function destroy(License $license)
    {
        $license->delete();

        return redirect()
            ->route('licenses.index')
            ->with('success', 'Licence supprimée avec succès.');
    }
}