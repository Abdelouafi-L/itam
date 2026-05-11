@extends('layouts.app')

@section('title', 'Modifier livraison')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('livraisons.show', $livraison) }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier — {{ $livraison->reference_interne }}
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

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>
                        La référence interne ne peut pas être modifiée.
                        Les lignes de détail sont gérées séparément.
                    </div>

                    <form method="POST"
                          action="{{ route('livraisons.update',
                                     $livraison) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Référence interne
                                </label>
                                <input type="text"
                                    class="form-control"
                                    value="{{ $livraison->reference_interne }}"
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Bon de livraison
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('bon_de_livraison')
                                           is-invalid @enderror"
                                    name="bon_de_livraison"
                                    value="{{ old('bon_de_livraison',
                                        $livraison->bon_de_livraison) }}"
                                    required>
                                @error('bon_de_livraison')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Fournisseur
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        name="fournisseur_id" required>
                                    @foreach($fournisseurs as $f)
                                        <option value="{{ $f->id }}"
                                            {{ old('fournisseur_id',
                                               $livraison->fournisseur_id)
                                               == $f->id
                                               ? 'selected' : '' }}>
                                            {{ $f->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Signataire
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        name="signataire_id" required>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ old('signataire_id',
                                               $livraison->signataire_id)
                                               == $emp->id
                                               ? 'selected' : '' }}>
                                            {{ $emp->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Date de livraison
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control"
                                    name="date_livraison"
                                    value="{{ old('date_livraison',
                                        $livraison->date_livraison
                                        ->format('Y-m-d')) }}"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Notes
                            </label>
                            <textarea class="form-control"
                                name="notes" rows="2">{{ old('notes',
                                    $livraison->notes) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('livraisons.show',
                                       $livraison) }}"
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