@extends('layouts.app')

@section('title', 'Livraison ' . $livraison->reference_interne)

@section('content')
<div class="container mt-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('livraisons.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">
                {{ $livraison->reference_interne }}
            </h2>
            <p class="text-muted small mb-0">
                BL Fournisseur: {{ $livraison->bon_de_livraison }} —
                {{ $livraison->date_livraison->format('d/m/Y') }}
            </p>
        </div>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <span class="badge fs-6 {{
                $livraison->statut === 'Réceptionnée'
                ? 'bg-success'
                : ($livraison->statut === 'En attente'
                   ? 'bg-warning text-dark'
                   : ($livraison->statut === 'Partielle'
                      ? 'bg-info'
                      : 'bg-secondary'))
            }}">
                {{ $livraison->statut }}
            </span>
            @if($livraison->isEnAttente() &&
                (Auth::user()->isAdmin() ||
                 Auth::user()->isTechnicien()))
            <a href="{{ route('livraisons.edit', $livraison) }}"
               class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- Header info --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>
                    Détails
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="45%">
                                Fournisseur
                            </td>
                            <td class="fw-medium">
                                <a href="{{ route('fournisseurs.show',
                                           $livraison->fournisseur) }}"
                                   class="text-decoration-none">
                                    {{ $livraison->fournisseur->nom
                                       ?? '—' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Signataire</td>
                            <td>
                                {{ $livraison->signataire->full_name
                                   ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ref. interne</td>
                            <td>
                                {{ $livraison->reference_interne }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bon livraison</td>
                            <td>{{ $livraison->bon_de_livraison }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>
                                {{ $livraison->date_livraison
                                   ->format('d/m/Y') }}
                            </td>
                        </tr>
                        @if($livraison->notes)
                        <tr>
                            <td class="text-muted">Notes</td>
                            <td class="small">
                                {{ $livraison->notes }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Line items --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-list-ul me-2"></i>
                    Produits livrés
                    ({{ $livraison->details->count() }} ligne(s))
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($livraison->details as $detail)
                            <tr>
                                <td>
                                    <span class="fw-medium">
                                        {{ $detail->product->name
                                           ?? '—' }}
                                    </span>
                                    <div class="small text-muted">
                                        {{ $detail->product->category
                                           ->name ?? '' }}
                                    </div>
                                    @if($detail->notes)
                                        <div class="small text-muted
                                                    fst-italic">
                                            {{ $detail->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $detail->quantite }}
                                </td>
                                <td class="text-end small">
                                    {{ $detail->prix_unitaire
                                       ? number_format(
                                           $detail->prix_unitaire, 2)
                                         . ' MAD'
                                       : '—' }}
                                </td>
                                <td class="text-end small fw-medium">
                                    {{ $detail->prix_unitaire
                                       ? number_format(
                                           $detail->total, 2) . ' MAD'
                                       : '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">
                                    Total livraison:
                                </td>
                                <td class="text-end">
                                    {{ number_format(
                                        $livraison->details->sum('total'),
                                        2) }} MAD
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Lifecycle actions --}}
            @if(!$livraison->isClosed() &&
                (Auth::user()->isAdmin() ||
                 Auth::user()->isTechnicien()))
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Actions — Lifecycle RF-35 à RF-37
                </div>
                <div class="card-body">

                    <div class="row g-3">

                        {{-- RF-35: Full reception --}}
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-check-circle
                                              text-success fs-3 mb-2
                                              d-block"></i>
                                    <p class="small fw-medium mb-2">
                                        Réception complète
                                    </p>
                                    <p class="small text-muted mb-3">
                                        Valide toutes les lignes.
                                        Met à jour le stock.
                                    </p>
                                    <form method="POST"
                                          action="{{ route(
                                              'livraisons.receptionner',
                                              $livraison) }}"
                                          onsubmit="return confirm(
                                              'Confirmer la réception complète ?\n' +
                                              'Le stock sera mis à jour automatiquement.'
                                          )">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-success
                                                       btn-sm w-100">
                                            <i class="bi bi-check2-all
                                                      me-1"></i>
                                            Réceptionner
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- RF-36: Partial reception --}}
                        <div class="col-md-4">
                            <div class="card border-info h-100">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-half text-info
                                              fs-3 mb-2 d-block"></i>
                                    <p class="small fw-medium mb-2">
                                        Réception partielle
                                    </p>
                                    <p class="small text-muted mb-3">
                                        Valide certaines quantités
                                        seulement.
                                    </p>
                                    <button type="button"
                                            class="btn btn-info btn-sm
                                                   w-100 text-white"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#partielleForm">
                                        <i class="bi bi-pencil me-1"></i>
                                        Saisir quantités
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- RF-37: Cancel --}}
                        <div class="col-md-4">
                            <div class="card border-danger h-100">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-x-circle
                                              text-danger fs-3 mb-2
                                              d-block"></i>
                                    <p class="small fw-medium mb-2">
                                        Annuler la livraison
                                    </p>
                                    <p class="small text-muted mb-3">
                                        Aucune mise à jour du stock.
                                    </p>
                                    <form method="POST"
                                          action="{{ route(
                                              'livraisons.annuler',
                                              $livraison) }}"
                                          onsubmit="return confirm(
                                              'Annuler cette livraison ?\n' +
                                              'Aucun stock ne sera mis à jour.'
                                          )">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-danger
                                                       btn-sm w-100">
                                            <i class="bi bi-x-lg me-1"></i>
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Partial form --}}
                    <div class="collapse mt-3" id="partielleForm">
                        <div class="card border-info">
                            <div class="card-header text-info fw-bold">
                                Saisir les quantités reçues
                            </div>
                            <div class="card-body">
                                <form method="POST"
                                      action="{{ route(
                                          'livraisons.partielle',
                                          $livraison) }}">
                                    @csrf
                                    @foreach($livraison->details
                                             as $detail)
                                    <div class="row align-items-center
                                                mb-3">
                                        <div class="col-md-6">
                                            <span class="fw-medium">
                                                {{ $detail->product
                                                   ->name ?? '—' }}
                                            </span>
                                            <div class="small text-muted">
                                                Commandé:
                                                {{ $detail->quantite }}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number"
                                                class="form-control
                                                       form-control-sm"
                                                name="details[{{ $detail->id }}][quantite]"
                                                value="0"
                                                min="0"
                                                max="{{ $detail->quantite }}">
                                        </div>
                                        <div class="col-md-3 small
                                                    text-muted">
                                            unités reçues
                                        </div>
                                    </div>
                                    @endforeach

                                    <button type="submit"
                                            class="btn btn-info
                                                   text-white btn-sm">
                                        <i class="bi bi-check me-1"></i>
                                        Valider réception partielle
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection