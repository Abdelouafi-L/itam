@extends('layouts.app')

@section('title', $fournisseur->nom)

@section('content')
<div class="container mt-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('fournisseurs.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-truck me-2 text-primary"></i>
                {{ $fournisseur->nom }}
            </h2>
            <p class="text-muted small mb-0">
                Historique des livraisons — RF-32
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <div class="ms-auto">
            <a href="{{ route('fournisseurs.edit', $fournisseur) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
        </div>
        @endif
    </div>

    <div class="row g-4">

        {{-- Supplier info --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Nom</td>
                            <td class="fw-medium">
                                {{ $fournisseur->nom }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Contact</td>
                            <td>{{ $fournisseur->contact_nom ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $fournisseur->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Téléphone</td>
                            <td>{{ $fournisseur->telephone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">N° TVA</td>
                            <td>{{ $fournisseur->numero_tva ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Site web</td>
                            <td>
                                @if($fournisseur->site_web)
                                    <a href="{{ $fournisseur->site_web }}"
                                       target="_blank"
                                       class="small">
                                        Visiter →
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @if($fournisseur->adresse)
                        <tr>
                            <td class="text-muted">Adresse</td>
                            <td class="small">
                                {{ $fournisseur->adresse }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Delivery history --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold
                             d-flex justify-content-between">
                    <span>
                        <i class="bi bi-clock-history me-2"></i>
                        Historique des livraisons
                        ({{ $fournisseur->livraisons->count() }})
                    </span>
                    @if(Auth::user()->isAdmin() ||
                        Auth::user()->isTechnicien())
                    <a href="{{ route('livraisons.create', ['fournisseur_id' => $fournisseur->id]) }}"
                    class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus me-1"></i>
                        Nouvelle livraison
                    </a>
                    @endif
                </div>

                @if($fournisseur->livraisons->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        Aucune livraison pour ce fournisseur.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Bon livraison</th>
                                <th>Date</th>
                                <th>Signataire</th>
                                <th>Statut</th>
                                <th class="text-center">Lignes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fournisseur->livraisons
                                     ->sortByDesc('date_livraison')
                                     as $livraison)
                            <tr>
                                <td class="fw-medium small">
                                    {{ $livraison->reference_interne }}
                                </td>
                                <td class="small text-muted">
                                    {{ $livraison->bon_de_livraison }}
                                </td>
                                <td class="small">
                                    {{ $livraison->date_livraison
                                       ->format('d/m/Y') }}
                                </td>
                                <td class="small">
                                    {{ $livraison->signataire
                                       ->full_name ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge {{
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
                                </td>
                                <td class="text-center small">
                                    {{ $livraison->details->count() }}
                                </td>
                                <td>
                                    <a href="{{ route('livraisons.show',
                                               $livraison) }}"
                                       class="btn btn-outline-info
                                              btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection