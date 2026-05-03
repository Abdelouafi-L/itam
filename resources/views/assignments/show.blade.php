@extends('layouts.app')

@section('title', 'Affectation #' . $assignment->id)

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('assignments.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">
                Affectation #{{ $assignment->id }}
            </h2>
            <p class="text-muted small mb-0">
                {{ $assignment->assigned_at->format('d/m/Y à H:i') }}
                — Créée par
                {{ $assignment->createdBy->full_name ?? '—' }}
            </p>
        </div>
        <div class="ms-auto">
            <span class="badge fs-6 {{
                $assignment->status === 'Active'
                ? 'bg-success' : 'bg-secondary'
            }}">
                {{ $assignment->status }}
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- Employee info --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-person me-2"></i>Employé
                </div>
                <div class="card-body">
                    <p class="fw-bold mb-1">
                        {{ $assignment->employee->full_name ?? '—' }}
                    </p>
                    <p class="text-muted small mb-1">
                        {{ $assignment->employee->department->name
                           ?? '—' }}
                    </p>
                    <p class="text-muted small mb-0">
                        {{ $assignment->employee->email ?? '—' }}
                    </p>
                    @if($assignment->notes)
                        <hr>
                        <p class="small mb-0">
                            <strong>Notes:</strong>
                            {{ $assignment->notes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assignment details --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-box-seam me-2"></i>
                    Produits affectés
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Qté</th>
                                <th class="text-center">Retourné</th>
                                <th>N° Série</th>
                                <th>Asset Tag</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignment->details as $detail)
                            <tr class="{{ $detail->isFullyReturned()
                                          ? 'table-secondary' : '' }}">
                                <td>
                                    <span class="fw-medium">
                                        {{ $detail->product->name
                                           ?? '—' }}
                                    </span>
                                    <div class="small text-muted">
                                        {{ $detail->product->category
                                           ->name ?? '' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{ $detail->quantity }}
                                </td>
                                <td class="text-center">
                                    {{ $detail->returned_qty }}
                                    @if($detail->isFullyReturned())
                                        <i class="bi bi-check-circle
                                                  text-success ms-1"></i>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $detail->serial_number ?? '—' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $detail->asset_tag ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Return form — RF-14 --}}
            @if($assignment->isActive() &&
                (Auth::user()->isAdmin() ||
                 Auth::user()->isTechnicien()))
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-arrow-return-left me-2"></i>
                    Enregistrer un retour — RF-14
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ route('assignments.return',
                                     $assignment) }}">
                        @csrf

                        @foreach($assignment->details as $detail)
                        @if(!$detail->isFullyReturned())
                        <div class="row align-items-center mb-3">
                            <div class="col-md-5">
                                <span class="fw-medium">
                                    {{ $detail->product->name ?? '—' }}
                                </span>
                                <div class="small text-muted">
                                    Restant à retourner:
                                    {{ $detail->quantity_out }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input
                                    type="number"
                                    class="form-control form-control-sm"
                                    name="details[{{ $detail->id }}][returned_qty]"
                                    value="0"
                                    min="0"
                                    max="{{ $detail->quantity_out }}"
                                >
                            </div>
                        </div>
                        @endif
                        @endforeach

                        <button type="submit"
                                class="btn btn-warning btn-sm">
                            <i class="bi bi-arrow-return-left me-2"></i>
                            Enregistrer le retour
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection