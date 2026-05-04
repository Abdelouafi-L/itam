@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-wrench me-2 text-primary"></i>
                Maintenance
            </h2>
            <p class="text-muted small mb-0">
                {{ $maintenances->total() }} intervention(s) —
                Coût total:
                <strong>
                    {{ number_format($totalCost, 2) }} MAD
                </strong>
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <a href="{{ route('maintenances.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouvelle maintenance
        </a>
        @endif
    </div>

    @if($maintenances->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune maintenance enregistrée.
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Équipement</th>
                            <th>Type</th>
                            <th>Technicien</th>
                            <th>Date</th>
                            <th class="text-end">Coût</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenances as $maintenance)
                        <tr>
                            <td class="fw-medium">
                                {{ $maintenance->hardware
                                   ->product->name ?? '—' }}
                                <div class="small text-muted">
                                    État:
                                    {{ $maintenance->hardware
                                       ->condition ?? '—' }}
                                </div>
                            </td>
                            <td>{{ $maintenance->type }}</td>
                            <td class="small">
                                {{ $maintenance->technician
                                   ->full_name ?? '—' }}
                            </td>
                            <td class="small">
                                {{ $maintenance->date
                                   ->format('d/m/Y') }}
                            </td>
                            <td class="text-end small">
                                {{ $maintenance->cost
                                   ? number_format($maintenance->cost, 2)
                                     . ' MAD' : '—' }}
                            </td>
                            <td>
                                <span class="badge {{
                                    $maintenance->status === 'Terminée'
                                    ? 'bg-success'
                                    : ($maintenance->status === 'En cours'
                                       ? 'bg-warning text-dark'
                                       : 'bg-secondary')
                                }}">
                                    {{ $maintenance->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('maintenances.show',
                                               $maintenance) }}"
                                       class="btn btn-outline-info"
                                       title="Détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin() ||
                                        Auth::user()->isTechnicien())
                                    <a href="{{ route('maintenances.edit',
                                               $maintenance) }}"
                                       class="btn btn-outline-primary"
                                       title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
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
            {{ $maintenances->links() }}
        </div>
    @endif

</div>
@endsection