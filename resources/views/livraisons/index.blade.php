@extends('layouts.app')

@section('title', 'Livraisons')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-in-down me-2 text-primary"></i>
                Livraisons
            </h2>
            <p class="text-muted small mb-0">
                {{ $livraisons->total() }} livraison(s)
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <a href="{{ route('livraisons.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouvelle livraison
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('livraisons.index') }}"
                  class="row g-3">
                <div class="col-md-3">
                    <select class="form-select form-select-sm"
                            name="statut">
                        <option value="">Tous les statuts</option>
                        @foreach(['En attente','Réceptionnée',
                                  'Partielle','Annulée'] as $s)
                            <option value="{{ $s }}"
                                {{ request('statut') == $s
                                   ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm"
                            name="fournisseur_id">
                        <option value="">Tous les fournisseurs</option>
                        @foreach($fournisseurs as $f)
                            <option value="{{ $f->id }}"
                                {{ request('fournisseur_id') == $f->id
                                   ? 'selected' : '' }}>
                                {{ $f->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date"
                           class="form-control form-control-sm"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           placeholder="Du">
                </div>
                <div class="col-md-2">
                    <input type="date"
                           class="form-control form-control-sm"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           placeholder="Au">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('livraisons.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    @if($livraisons->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune livraison trouvée.
        </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Référence interne</th>
                        <th>Bon de livraison</th>
                        <th>Fournisseur</th>
                        <th>Signataire</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="text-center">Lignes</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($livraisons as $livraison)
                    <tr>
                        <td class="fw-medium small">
                            {{ $livraison->reference_interne }}
                        </td>
                        <td class="small text-muted">
                            {{ $livraison->bon_de_livraison }}
                        </td>
                        <td class="small">
                            {{ $livraison->fournisseur->nom ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $livraison->signataire->full_name ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $livraison->date_livraison
                               ->format('d/m/Y') }}
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
                        <td class="text-center">
                            {{-- View button — always visible --}}
                            <a href="{{ route('livraisons.show', $livraison) }}"
                            class="btn btn-outline-info btn-sm"
                            title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>

                            @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())

                                {{-- Edit — only En attente --}}
                                @if($livraison->isEnAttente())
                                    <a href="{{ route('livraisons.edit', $livraison) }}"
                                    class="btn btn-outline-warning btn-sm"
                                    title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif

                                {{-- Réceptionner — En attente or Partielle --}}
                                @if($livraison->isEnAttente() || $livraison->statut === 'Partielle')
                                    <form method="POST"
                                        action="{{ route('livraisons.receptionner', $livraison) }}"
                                        onsubmit="return confirm('Confirmer la réception complète ?')"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-outline-success btn-sm"
                                                title="Réceptionner">
                                            <i class="bi bi-check2-all"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Annuler — En attente or Partielle --}}
                                @if($livraison->isEnAttente() || $livraison->statut === 'Partielle')
                                    <form method="POST"
                                        action="{{ route('livraisons.annuler', $livraison) }}"
                                        onsubmit="return confirm('Annuler cette livraison ?')"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Annuler">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif

                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $livraisons->links() }}
    </div>
    @endif

</div>
@endsection