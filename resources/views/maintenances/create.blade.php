@extends('layouts.app')

@section('title', 'Nouvelle maintenance')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('maintenances.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-wrench me-2 text-primary"></i>
                    Nouvelle maintenance
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
                          action="{{ route('maintenances.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="hardware_id"
                                       class="form-label fw-medium">
                                    Équipement
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('hardware_id')
                                           is-invalid @enderror"
                                    id="hardware_id"
                                    name="hardware_id"
                                    required
                                >
                                    <option value="">
                                        Choisir un équipement...
                                    </option>
                                    @foreach($hardwares as $hardware)
                                        <option
                                            value="{{ $hardware->id }}"
                                            {{ old('hardware_id') ==
                                               $hardware->id
                                               ? 'selected' : '' }}>
                                            {{ $hardware->product->name
                                               ?? '—' }}
                                            ({{ $hardware->condition }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('hardware_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="technician_id"
                                       class="form-label fw-medium">
                                    Technicien responsable
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('technician_id')
                                           is-invalid @enderror"
                                    id="technician_id"
                                    name="technician_id"
                                    required
                                >
                                    <option value="">
                                        Choisir un technicien...
                                    </option>
                                    @foreach($technicians as $tech)
                                        <option
                                            value="{{ $tech->id }}"
                                            {{ old('technician_id',
                                               Auth::id()) == $tech->id
                                               ? 'selected' : '' }}>
                                            {{ $tech->full_name }}
                                            ({{ $tech->getRoleNames()->first() ?? '—' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type"
                                       class="form-label fw-medium">
                                    Type d'intervention
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="type" name="type" required>
                                    <option value="">Choisir...</option>
                                    @foreach([
                                        'Préventive',
                                        'Corrective',
                                        'Mise à niveau',
                                        'Nettoyage'
                                    ] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('type') == $type
                                               ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="date"
                                       class="form-label fw-medium">
                                    Date d'intervention
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    class="form-control
                                           @error('date')
                                           is-invalid @enderror"
                                    id="date"
                                    name="date"
                                    value="{{ old('date',
                                        now()->format('Y-m-d')) }}"
                                    required
                                >
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status"
                                       class="form-label fw-medium">
                                    Statut
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="status"
                                        name="status">
                                    @foreach([
                                        'Planifiée',
                                        'En cours',
                                        'Terminée'
                                    ] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status',
                                               'Planifiée') == $s
                                               ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cost"
                                       class="form-label fw-medium">
                                    Coût (MAD)
                                </label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="cost"
                                    name="cost"
                                    value="{{ old('cost') }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                >
                            </div>
                            <div class="col-md-4">
                                <label for="condition"
                                       class="form-label fw-medium">
                                    Nouvel état
                                </label>
                                <select class="form-select"
                                        id="condition"
                                        name="condition">
                                    <option value="">
                                        Inchangé
                                    </option>
                                    @foreach([
                                        'Neuf',
                                        'Bon',
                                        'Usagé',
                                        'Endommagé'
                                    ] as $c)
                                        <option value="{{ $c }}"
                                            {{ old('condition') == $c
                                               ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description"
                                   class="form-label fw-medium">
                                Description
                            </label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Détails de l'intervention..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('maintenances.index') }}"
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