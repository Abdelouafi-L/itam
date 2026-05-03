@extends('layouts.app')

@section('title', 'Affectations')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-arrow-left-right me-2 text-primary"></i>
                Affectations
            </h2>
            <p class="text-muted small mb-0">
                {{ $assignments->total() }} affectation(s) au total
            </p>
        </div>
        @can('create', App\Models\Assignment::class)
        @endcan
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <a href="{{ route('assignments.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouvelle affectation
        </a>
        @endif
    </div>

    @if($assignments->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune affectation trouvée.
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employé</th>
                            <th>Produits</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Créé par</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td class="text-muted small">
                                {{ $assignment->id }}
                            </td>
                            <td class="fw-medium">
                                {{ $assignment->employee->full_name
                                   ?? '—' }}
                                <div class="text-muted small">
                                    {{ $assignment->employee
                                       ->department->name ?? '' }}
                                </div>
                            </td>
                            <td>
                                @foreach($assignment->details as $detail)
                                    <span class="badge bg-light
                                                 text-dark border me-1">
                                        {{ $detail->product->name ?? '—' }}
                                        ({{ $detail->quantity }})
                                    </span>
                                @endforeach
                            </td>
                            <td class="small">
                                {{ $assignment->assigned_at
                                   ->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge {{
                                    $assignment->status === 'Active'
                                    ? 'bg-success' : 'bg-secondary'
                                }}">
                                    {{ $assignment->status }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $assignment->createdBy
                                   ->full_name ?? '—' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('assignments.show',
                                           $assignment) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $assignments->links() }}
        </div>
    @endif

</div>
@endsection