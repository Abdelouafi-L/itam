@extends('layouts.app')

@section('title', 'Maintenance #' . $maintenance->id)

@section('content')
<div class="container mt-4">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('maintenances.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">
                Maintenance #{{ $maintenance->id }}
            </h2>
            <p class="text-muted small mb-0">
                {{ $maintenance->date->format('d/m/Y') }}
            </p>
        </div>
        <div class="ms-auto d-flex gap-2">
            @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
            <a href="{{ route('maintenances.edit', $maintenance) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- Maintenance details --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-wrench me-2"></i>
                    Détails intervention
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">
                                Équipement
                            </td>
                            <td class="fw-medium">
                                {{ $maintenance->hardware
                                   ->product->name ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">État actuel</td>
                            <td>
                                {{ $maintenance->hardware
                                   ->condition ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Type</td>
                            <td>{{ $maintenance->type }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Technicien</td>
                            <td>
                                {{ $maintenance->technician
                                   ->full_name ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>
                                {{ $maintenance->date->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Coût</td>
                            <td>
                                {{ $maintenance->cost
                                   ? number_format($maintenance->cost, 2)
                                     . ' MAD' : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Statut</td>
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
                        </tr>
                        @if($maintenance->description)
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>{{ $maintenance->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- RF-23 Retire asset — Admin only --}}
        @if(Auth::user()->isAdmin())
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Zone de danger — RF-23
                </div>
                <div class="card-body">
                    <p class="text-danger fw-medium">
                        Retirer définitivement cet équipement du parc
                    </p>
                    <p class="small text-muted">
                        Cette action est <strong>irréversible</strong>.
                        L'équipement sera décommissionné et son stock
                        sera mis à zéro.
                    </p>

                    <form method="POST"
                          action="{{ route('hardware.retire',
                                     $maintenance->hardware) }}"
                          onsubmit="return confirm(
                              'ATTENTION: Cette action est irréversible.\n' +
                              'Confirmez-vous le retrait définitif de cet équipement ?'
                          )">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium small">
                                Tapez <strong>RETIRER</strong>
                                pour confirmer
                            </label>
                            <input
                                type="text"
                                class="form-control form-control-sm
                                       @error('confirmation')
                                       is-invalid @enderror"
                                name="confirmation"
                                placeholder="RETIRER"
                            >
                            @error('confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit"
                                class="btn btn-danger btn-sm">
                            <i class="bi bi-trash me-2"></i>
                            Retirer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection