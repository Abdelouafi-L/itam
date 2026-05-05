@extends('layouts.app')

@section('title', 'Rapport — Coûts de maintenance')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-wrench me-2 text-primary"></i>
                Coûts de maintenance — RF-27
            </h2>
            <p class="text-muted small mb-0">
                {{ $summary['total'] }} intervention(s) —
                Coût total:
                {{ number_format($summary['total_cost'], 2) }} MAD
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rapports.maintenances.pdf') }}"
               class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('rapports.maintenances.csv') }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-spreadsheet me-1"></i> CSV
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-primary">
                    {{ $summary['total'] }}
                </div>
                <div class="small text-muted">Total interventions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-success">
                    {{ $summary['completed'] }}
                </div>
                <div class="small text-muted">Terminées</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-warning">
                    {{ $summary['in_progress'] }}
                </div>
                <div class="small text-muted">En cours</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-danger">
                    {{ number_format($summary['total_cost'], 2) }}
                </div>
                <div class="small text-muted">MAD total</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('rapports.maintenances') }}"
                  class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Statut
                    </label>
                    <select class="form-select form-select-sm"
                            name="status">
                        <option value="">Tous</option>
                        @foreach(['Planifiée','En cours','Terminée']
                                 as $s)
                            <option value="{{ $s }}"
                                {{ request('status') == $s
                                   ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">
                        Du
                    </label>
                    <input type="date"
                           class="form-control form-control-sm"
                           name="date_from"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">
                        Au
                    </label>
                    <input type="date"
                           class="form-control form-control-sm"
                           name="date_to"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('rapports.maintenances') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Équipement</th>
                        <th>Type</th>
                        <th>Technicien</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th class="text-end">Coût (MAD)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maintenance)
                    <tr>
                        <td class="fw-medium">
                            {{ $maintenance->hardware->product->name
                               ?? '—' }}
                        </td>
                        <td>{{ $maintenance->type }}</td>
                        <td class="small">
                            {{ $maintenance->technician->full_name
                               ?? '—' }}
                        </td>
                        <td class="small">
                            {{ $maintenance->date->format('d/m/Y') }}
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
                        <td class="text-end">
                            {{ $maintenance->cost
                               ? number_format($maintenance->cost, 2)
                               : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6"
                            class="text-center text-muted py-4">
                            Aucune maintenance trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">
                            Coût total:
                        </td>
                        <td class="text-end">
                            {{ number_format(
                                $summary['total_cost'], 2) }} MAD
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection