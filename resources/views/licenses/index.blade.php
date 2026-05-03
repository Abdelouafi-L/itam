@extends('layouts.app')

@section('title', 'Licences logicielles')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-key me-2 text-primary"></i>
                Licences logicielles
            </h2>
            <p class="text-muted small mb-0">
                {{ $licenses->count() }} licence(s) au total
            </p>
        </div>
        <a href="{{ route('licenses.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouvelle licence
        </a>
    </div>

    @if($licenses->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune licence trouvée.
            <a href="{{ route('licenses.create') }}">
                Ajouter la première licence
            </a>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Logiciel</th>
                            <th class="text-center">Sièges</th>
                            <th class="text-center">Disponibles</th>
                            <th>Expiration</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licenses as $license)
                        <tr class="{{ $license->isExpiringSoon()
                                      ? 'table-warning' : '' }}">
                            <td class="fw-medium">
                                {{ $license->software->product->name
                                   ?? '—' }}
                                <div class="text-muted small">
                                    {{ $license->software->version
                                       ?? '' }}
                                </div>
                            </td>
                            <td class="text-center">
                                {{ $license->seats_used }}
                                / {{ $license->seats_total }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{
                                    $license->isOutOfSeats()
                                    ? 'bg-danger' : 'bg-success'
                                }}">
                                    {{ $license->seats_available }}
                                </span>
                            </td>
                            <td>
                                @if($license->expiry_date)
                                    {{ $license->expiry_date
                                       ->format('d/m/Y') }}
                                    @if($license->days_remaining !== null)
                                        <div class="small {{
                                            $license->days_remaining <= 30
                                            ? 'text-danger fw-bold'
                                            : 'text-muted'
                                        }}">
                                            @if($license->days_remaining < 0)
                                                Expirée
                                            @else
                                                {{ $license->days_remaining }}
                                                jour(s) restant(s)
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{
                                    $license->status === 'Active'
                                    ? 'bg-success'
                                    : ($license->status === 'Expirée'
                                       ? 'bg-danger' : 'bg-secondary')
                                }}">
                                    {{ $license->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('licenses.show',
                                               $license) }}"
                                       class="btn btn-outline-info"
                                       title="Détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('licenses.edit',
                                               $license) }}"
                                       class="btn btn-outline-primary"
                                       title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('licenses.destroy',
                                                     $license) }}"
                                          onsubmit="return confirm(
                                              'Supprimer cette licence ?')"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection