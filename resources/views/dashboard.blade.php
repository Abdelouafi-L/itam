@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>
            Tableau de bord
        </h2>
        <p class="text-muted small mb-0">
            Bienvenue, {{ Auth::user()->full_name }} —
            {{ Auth::user()->role->name }} —
            {{ now()->format('d/m/Y') }}
        </p>
    </div>

    {{-- Alerts --}}
    @if($expiringLicenses->isNotEmpty())
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>{{ $expiringLicenses->count() }}</strong>
        licence(s) expire(nt) dans moins de 30 jours.
        <a href="{{ route('licenses.index') }}" class="alert-link">
            Voir les licences →
        </a>
        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($outOfSeatsLicenses->isNotEmpty())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-x-circle me-2"></i>
        <strong>{{ $outOfSeatsLicenses->count() }}</strong>
        licence(s) sans sièges disponibles.
        <a href="{{ route('licenses.index') }}" class="alert-link">
            Voir les licences →
        </a>
        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($maintenancesInProgress > 0)
    <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-wrench me-2"></i>
        <strong>{{ $maintenancesInProgress }}</strong>
        maintenance(s) en cours.
        <a href="{{ route('maintenances.index') }}" class="alert-link">
            Voir les maintenances →
        </a>
        <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between
                                align-items-start">
                        <div>
                            <p class="text-muted small mb-1">
                                Total produits
                            </p>
                            <h3 class="fw-bold mb-0">
                                {{ $totalProducts }}
                            </h3>
                            <p class="text-muted small mb-0">
                                {{ $totalItems }} unités
                            </p>
                        </div>
                        <div class="bg-primary bg-opacity-10
                                    rounded p-2">
                            <i class="bi bi-box-seam fs-4
                                      text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between
                                align-items-start">
                        <div>
                            <p class="text-muted small mb-1">
                                Disponibles
                            </p>
                            <h3 class="fw-bold mb-0 text-success">
                                {{ $totalAvailable }}
                            </h3>
                            <p class="text-muted small mb-0">
                                unités en stock
                            </p>
                        </div>
                        <div class="bg-success bg-opacity-10
                                    rounded p-2">
                            <i class="bi bi-check-circle fs-4
                                      text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between
                                align-items-start">
                        <div>
                            <p class="text-muted small mb-1">
                                Affectés
                            </p>
                            <h3 class="fw-bold mb-0 text-warning">
                                {{ $totalAssigned }}
                            </h3>
                            <p class="text-muted small mb-0">
                                unités assignées
                            </p>
                        </div>
                        <div class="bg-warning bg-opacity-10
                                    rounded p-2">
                            <i class="bi bi-people fs-4
                                      text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between
                                align-items-start">
                        <div>
                            <p class="text-muted small mb-1">
                                Coût maintenance
                            </p>
                            <h3 class="fw-bold mb-0 text-danger">
                                {{ number_format(
                                    $totalMaintenanceCost, 0
                                ) }}
                            </h3>
                            <p class="text-muted small mb-0">
                                MAD total
                            </p>
                        </div>
                        <div class="bg-danger bg-opacity-10
                                    rounded p-2">
                            <i class="bi bi-wrench fs-4
                                      text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4">

        {{-- Stock by Category Chart --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-pie-chart me-2"></i>
                    Produits par catégorie
                </div>
                <div class="card-body d-flex justify-content-center
                             align-items-center">
                    <canvas id="categoryChart"
                            style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Assignment Status Chart --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-bar-chart me-2"></i>
                    Statut des affectations
                </div>
                <div class="card-body d-flex justify-content-center
                             align-items-center">
                    <canvas id="assignmentChart"
                            style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Activity Row --}}
    <div class="row g-4">

        {{-- Recent Assignments --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold
                             d-flex justify-content-between">
                    <span>
                        <i class="bi bi-clock-history me-2"></i>
                        Dernières affectations
                    </span>
                    <a href="{{ route('assignments.index') }}"
                       class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentAssignments->isEmpty())
                        <p class="text-muted text-center py-3 small">
                            Aucune affectation
                        </p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($recentAssignments as $assignment)
                        <a href="{{ route('assignments.show',
                                   $assignment) }}"
                           class="list-group-item list-group-item-action
                                  py-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium small">
                                    {{ $assignment->employee->full_name
                                       ?? '—' }}
                                </span>
                                <span class="badge {{
                                    $assignment->status === 'Active'
                                    ? 'bg-success' : 'bg-secondary'
                                }} rounded-pill">
                                    {{ $assignment->status }}
                                </span>
                            </div>
                            <div class="text-muted small">
                                {{ $assignment->assigned_at
                                   ->format('d/m/Y') }} —
                                {{ $assignment->details->count() }}
                                produit(s)
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Expiring Licenses --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold
                             d-flex justify-content-between">
                    <span>
                        <i class="bi bi-key me-2"></i>
                        Licences expirant bientôt
                    </span>
                    <a href="{{ route('licenses.index') }}"
                       class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($expiringLicenses->isEmpty())
                        <p class="text-muted text-center py-3 small">
                            Aucune licence expirant bientôt
                        </p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($expiringLicenses as $license)
                        <a href="{{ route('licenses.show', $license) }}"
                           class="list-group-item list-group-item-action
                                  py-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium small">
                                    {{ $license->software->product->name
                                       ?? '—' }}
                                </span>
                                <span class="badge {{
                                    $license->days_remaining <= 7
                                    ? 'bg-danger'
                                    : 'bg-warning text-dark'
                                }} rounded-pill">
                                    {{ $license->days_remaining }}j
                                </span>
                            </div>
                            <div class="text-muted small">
                                Expire le:
                                {{ $license->expiry_date
                                   ->format('d/m/Y') }} —
                                {{ $license->seats_available }}
                                siège(s) disponible(s)
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Stock by Category — Doughnut Chart
const categoryCtx = document.getElementById('categoryChart');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($stockByCategory->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($stockByCategory->pluck('count')) !!},
            backgroundColor: [
                '#0d6efd', '#198754', '#ffc107',
                '#dc3545', '#6f42c1', '#0dcaf0'
            ],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Assignment Status — Bar Chart
const assignmentCtx = document.getElementById('assignmentChart');
new Chart(assignmentCtx, {
    type: 'bar',
    data: {
        labels: ['Active', 'Clôturée'],
        datasets: [{
            label: 'Affectations',
            data: [
                {{ $assignmentStats['active'] }},
                {{ $assignmentStats['closed'] }}
            ],
            backgroundColor: ['#198754', '#6c757d'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush