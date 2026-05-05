@extends('layouts.app')

@section('title', 'Rapport — Conformité des licences')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-key me-2 text-primary"></i>
                Conformité des licences — RF-26
            </h2>
            <p class="text-muted small mb-0">
                {{ $summary['total'] }} licence(s) —
                {{ $summary['active'] }} active(s) —
                {{ $summary['expiring'] }} expirant bientôt —
                {{ $summary['expired'] }} expirée(s)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rapports.licenses.pdf') }}"
               class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('rapports.licenses.csv') }}"
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
                <div class="small text-muted">Total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-success">
                    {{ $summary['active'] }}
                </div>
                <div class="small text-muted">Actives</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-warning">
                    {{ $summary['expiring'] }}
                </div>
                <div class="small text-muted">Expirant bientôt</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="fs-3 fw-bold text-danger">
                    {{ $summary['expired'] }}
                </div>
                <div class="small text-muted">Expirées</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('rapports.licenses') }}"
                  class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Statut
                    </label>
                    <select class="form-select form-select-sm"
                            name="status">
                        <option value="">Tous</option>
                        @foreach(['Active','Expirée','Résiliée'] as $s)
                            <option value="{{ $s }}"
                                {{ request('status') == $s
                                   ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Expiration
                    </label>
                    <select class="form-select form-select-sm"
                            name="expiring">
                        <option value="">Toutes</option>
                        <option value="1"
                            {{ request('expiring') ? 'selected' : '' }}>
                            Expirant dans 30 jours
                        </option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('rapports.licenses') }}"
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
                        <th>Logiciel</th>
                        <th>Statut</th>
                        <th class="text-center">Sièges</th>
                        <th class="text-center">Utilisés</th>
                        <th class="text-center">Disponibles</th>
                        <th>Expiration</th>
                        <th class="text-end">Coût (MAD)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenses as $license)
                    <tr class="{{ $license->isExpiringSoon()
                                  ? 'table-warning' : '' }}">
                        <td class="fw-medium">
                            {{ $license->software->product->name
                               ?? '—' }}
                            <div class="small text-muted">
                                {{ $license->software->version ?? '' }}
                            </div>
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
                            {{ $license->seats_total }}
                        </td>
                        <td class="text-center">
                            {{ $license->seats_used }}
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
                            {{ $license->expiry_date
                               ?->format('d/m/Y') ?? '—' }}
                            @if($license->days_remaining !== null)
                                <div class="small {{
                                    $license->days_remaining <= 30
                                    ? 'text-danger' : 'text-muted'
                                }}">
                                    {{ $license->days_remaining }}j
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ $license->cost
                               ? number_format($license->cost, 2)
                               : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7"
                            class="text-center text-muted py-4">
                            Aucune licence trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection