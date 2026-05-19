@extends('layouts.app')

@section('title', 'Modifier licence')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('licenses.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier licence —
                    {{ $license->software->product->name ?? '' }}
                </h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('licenses.update', $license) }}">
                        @csrf
                        @method('PUT')

                        {{-- Software name — read only --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                Logiciel
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $license->software->product->name
                                         ?? '—' }}" disabled>
                            <div class="form-text">
                                Le logiciel ne peut pas être modifié.
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="seats_total"
                                       class="form-label fw-medium">
                                    Nombre de sièges total
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control
                                           @error('seats_total')
                                           is-invalid @enderror"
                                    id="seats_total"
                                    name="seats_total"
                                    value="{{ old('seats_total',
                                        $license->seats_total) }}"
                                    min="1"
                                    required
                                >
                                @error('seats_total')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="seats_used"
                                       class="form-label fw-medium">
                                    Sièges utilisés
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control bg-light"
                                    id="seats_used"
                                    value="{{ $license->seats_used }}"
                                    disabled
                                >
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Géré automatiquement par les affectations — non modifiable.
                                </div>
                                @error('seats_used')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cost"
                                       class="form-label fw-medium">
                                    Coût (MAD)
                                </label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="cost"
                                    name="cost"
                                    value="{{ old('cost', $license->cost) }}"
                                    min="0"
                                    step="0.01"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="status"
                                       class="form-label fw-medium">
                                    Statut
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="status"
                                        name="status">
                                    @foreach(['Active','Expirée',
                                              'Résiliée'] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status',
                                               $license->status) == $s
                                               ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="purchase_date"
                                       class="form-label fw-medium">
                                    Date d'achat
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="purchase_date"
                                    name="purchase_date"
                                    value="{{ old('purchase_date',
                                        $license->purchase_date
                                        ?->format('Y-m-d')) }}"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date"
                                       class="form-label fw-medium">
                                    Date d'expiration
                                </label>
                                <input
                                    type="date"
                                    class="form-control
                                           @error('expiry_date')
                                           is-invalid @enderror"
                                    id="expiry_date"
                                    name="expiry_date"
                                    value="{{ old('expiry_date',
                                        $license->expiry_date
                                        ?->format('Y-m-d')) }}"
                                >
                                @error('expiry_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('licenses.index') }}"
                               class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection