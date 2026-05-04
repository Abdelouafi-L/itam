@extends('layouts.app')

@section('title', 'Modifier maintenance #' . $maintenance->id)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('maintenances.show', $maintenance) }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier maintenance #{{ $maintenance->id }}
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

                    {{-- Equipment — read only --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Équipement
                        </label>
                        <input type="text" class="form-control"
                            value="{{ $maintenance->hardware
                                     ->product->name ?? '—' }}"
                            disabled>
                    </div>

                    <form method="POST"
                          action="{{ route('maintenances.update',
                                     $maintenance) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technician_id"
                                       class="form-label fw-medium">
                                    Technicien
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="technician_id"
                                        name="technician_id" required>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}"
                                            {{ old('technician_id',
                                               $maintenance->technician_id)
                                               == $tech->id
                                               ? 'selected' : '' }}>
                                            {{ $tech->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type"
                                       class="form-label fw-medium">
                                    Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="type" name="type" required>
                                    @foreach([
                                        'Préventive',
                                        'Corrective',
                                        'Mise à niveau',
                                        'Nettoyage'
                                    ] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('type',
                                               $maintenance->type)
                                               == $type
                                               ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date"
                                       class="form-label fw-medium">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control"
                                    id="date" name="date"
                                    value="{{ old('date',
                                        $maintenance->date
                                        ->format('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="status"
                                       class="form-label fw-medium">
                                    Statut
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        id="status" name="status">
                                    @foreach([
                                        'Planifiée',
                                        'En cours',
                                        'Terminée'
                                    ] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status',
                                               $maintenance->status)
                                               == $s
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
                                <input type="number"
                                    class="form-control"
                                    id="cost" name="cost"
                                    value="{{ old('cost',
                                        $maintenance->cost) }}"
                                    min="0" step="0.01">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="condition"
                                       class="form-label fw-medium">
                                    Nouvel état
                                </label>
                                <select class="form-select"
                                        id="condition" name="condition">
                                    <option value="">Inchangé</option>
                                    @foreach([
                                        'Neuf','Bon','Usagé','Endommagé'
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
                            <textarea class="form-control"
                                id="description" name="description"
                                rows="3"
                            >{{ old('description',
                                $maintenance->description) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('maintenances.show',
                                       $maintenance) }}"
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