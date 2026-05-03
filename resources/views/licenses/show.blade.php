@extends('layouts.app')

@section('title', 'Licence — ' . ($license->software->product->name ?? ''))

@section('content')
<div class="container mt-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('licenses.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">
                {{ $license->software->product->name ?? '—' }}
            </h2>
            <p class="text-muted small mb-0">
                Détails de la licence
            </p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('licenses.edit', $license) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- License info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-key me-2"></i>Informations licence
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="45%">
                                Logiciel
                            </td>
                            <td class="fw-medium">
                                {{ $license->software->product->name
                                   ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Version</td>
                            <td>
                                {{ $license->software->version ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Statut</td>
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
                        </tr>
                        <tr>
                            <td class="text-muted">Coût</td>
                            <td>
                                {{ $license->cost
                                   ? number_format($license->cost, 2)
                                     . ' MAD' : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date d'achat</td>
                            <td>
                                {{ $license->purchase_date
                                   ?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Expiration</td>
                            <td>
                                {{ $license->expiry_date
                                   ?->format('d/m/Y') ?? '—' }}
                                @if($license->days_remaining !== null)
                                    <span class="badge ms-1 {{
                                        $license->days_remaining < 0
                                        ? 'bg-danger'
                                        : ($license->days_remaining <= 30
                                           ? 'bg-warning text-dark'
                                           : 'bg-success')
                                    }}">
                                        @if($license->days_remaining < 0)
                                            Expirée
                                        @else
                                            {{ $license->days_remaining }}j
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Seats info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-people me-2"></i>
                    Sièges — RF-18
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold text-primary">
                                    {{ $license->seats_total }}
                                </div>
                                <div class="small text-muted">Total</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold text-warning">
                                    {{ $license->seats_used }}
                                </div>
                                <div class="small text-muted">Utilisés</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold {{
                                    $license->isOutOfSeats()
                                    ? 'text-danger' : 'text-success'
                                }}">
                                    {{ $license->seats_available }}
                                </div>
                                <div class="small text-muted">
                                    Disponibles
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($license->isOutOfSeats())
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Plus aucun siège disponible — RF-18
                        </div>
                    @endif

                    @if($license->isExpiringSoon())
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Expire dans {{ $license->days_remaining }}
                            jour(s) — notification RF-20 active
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection