@extends('layouts.app')

@section('title', 'Fournisseurs')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-truck me-2 text-primary"></i>
                Fournisseurs
            </h2>
            <p class="text-muted small mb-0">
                {{ $fournisseurs->total() }} fournisseur(s)
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <a href="{{ route('fournisseurs.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouveau fournisseur
        </a>
        @endif
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('fournisseurs.index') }}"
                  class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Rechercher par nom, email, contact...">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Rechercher
                    </button>
                    @if(request('search'))
                    <a href="{{ route('fournisseurs.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x me-1"></i> Effacer
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    @if($fournisseurs->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucun fournisseur trouvé.
        </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th class="text-center">Livraisons</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fournisseurs as $fournisseur)
                    <tr>
                        <td class="fw-medium">
                            {{ $fournisseur->nom }}
                            @if($fournisseur->numero_tva)
                                <div class="small text-muted">
                                    TVA: {{ $fournisseur->numero_tva }}
                                </div>
                            @endif
                        </td>
                        <td class="small">
                            {{ $fournisseur->contact_nom ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $fournisseur->email ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $fournisseur->telephone ?? '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill">
                                {{ $fournisseur->livraisons_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('fournisseurs.show',
                                           $fournisseur) }}"
                                   class="btn btn-outline-info"
                                   title="Historique">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() ||
                                    Auth::user()->isTechnicien())
                                <a href="{{ route('fournisseurs.edit',
                                           $fournisseur) }}"
                                   class="btn btn-outline-primary"
                                   title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($fournisseur->livraisons_count > 0)
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            style="padding: 8px 12px;"
                                            title="{{ $fournisseur->livraisons_count }} livraison(s) liée(s) — suppression impossible"
                                            data-bs-toggle="tooltip"
                                            data-bs-trigger="hover">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('fournisseurs.destroy', $fournisseur) }}"
                                        onsubmit="return confirm('Supprimer ce fournisseur ?')"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
@endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $fournisseurs->links() }}
    </div>
    @endif

</div>
@endsection