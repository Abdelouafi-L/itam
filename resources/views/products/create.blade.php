@extends('layouts.app')

@section('title', 'Nouveau produit')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Nouveau produit
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
                          action="{{ route('products.store') }}">
                        @csrf

                        {{-- Common fields --}}
                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Informations générales
                        </h6>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name"
                                       class="form-label fw-medium">
                                    Nom du produit
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control
                                           @error('name') is-invalid @enderror"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Ex: Dell Latitude 5540"
                                    autofocus
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category_id"
                                       class="form-label fw-medium">
                                    Catégorie
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('category_id')
                                           is-invalid @enderror"
                                    id="category_id"
                                    name="category_id"
                                    required
                                >
                                    <option value="">
                                        Choisir...
                                    </option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id
                                               ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="brand"
                                       class="form-label fw-medium">
                                    Marque
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="brand"
                                    name="brand"
                                    value="{{ old('brand') }}"
                                    placeholder="Ex: Dell, HP, Microsoft"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="model"
                                       class="form-label fw-medium">
                                    Modèle
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="model"
                                    name="model"
                                    value="{{ old('model') }}"
                                    placeholder="Ex: Latitude 5540"
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description"
                                   class="form-label fw-medium">
                                Description
                            </label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="2"
                                placeholder="Description optionnelle..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- Type selector --}}
                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Type de produit
                            <span class="text-danger">*</span>
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-check-inline
                                            w-100">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="type"
                                        id="type_hardware"
                                        value="hardware"
                                        {{ old('type') == 'hardware'
                                           ? 'checked' : '' }}
                                        required
                                    >
                                    <label class="form-check-label
                                                  btn btn-outline-danger
                                                  w-100 text-start p-3"
                                           for="type_hardware">
                                        <i class="bi bi-cpu fs-4 me-2"></i>
                                        <strong>Hardware</strong>
                                        <br>
                                        <small class="text-muted">
                                            Équipement physique
                                        </small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-inline
                                            w-100">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="type"
                                        id="type_software"
                                        value="software"
                                        {{ old('type') == 'software'
                                           ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label
                                                  btn btn-outline-warning
                                                  w-100 text-start p-3"
                                           for="type_software">
                                        <i class="bi bi-code-square
                                                  fs-4 me-2"></i>
                                        <strong>Software</strong>
                                        <br>
                                        <small class="text-muted">
                                            Logiciel / Application
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Hardware specific fields --}}
                        <div id="hardware_fields" style="display:none">
                            <h6 class="fw-bold text-muted text-uppercase
                                       small mb-3">
                                Détails matériel
                            </h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="condition"
                                           class="form-label fw-medium">
                                        État
                                    </label>
                                    <select class="form-select"
                                            id="condition"
                                            name="condition">
                                        <option value="Neuf"
                                            {{ old('condition') == 'Neuf'
                                               ? 'selected' : '' }}>
                                            Neuf
                                        </option>
                                        <option value="Bon"
                                            {{ old('condition') == 'Bon'
                                               ? 'selected' : '' }}>
                                            Bon
                                        </option>
                                        <option value="Usagé"
                                            {{ old('condition') == 'Usagé'
                                               ? 'selected' : '' }}>
                                            Usagé
                                        </option>
                                        <option value="Endommagé"
                                            {{ old('condition') == 'Endommagé'
                                               ? 'selected' : '' }}>
                                            Endommagé
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="purchase_date"
                                           class="form-label fw-medium">
                                        Date d'achat
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="purchase_date"
                                        name="purchase_date"
                                        value="{{ old('purchase_date') }}"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <label for="warranty_date"
                                           class="form-label fw-medium">
                                        Fin de garantie
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="warranty_date"
                                        name="warranty_date"
                                        value="{{ old('warranty_date') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Software specific fields --}}
                        <div id="software_fields" style="display:none">
                            <h6 class="fw-bold text-muted text-uppercase
                                       small mb-3">
                                Détails logiciel
                            </h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="version"
                                           class="form-label fw-medium">
                                        Version
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="version"
                                        name="version"
                                        value="{{ old('version') }}"
                                        placeholder="Ex: 2024.1"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <label for="publisher"
                                           class="form-label fw-medium">
                                        Éditeur
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="publisher"
                                        name="publisher"
                                        value="{{ old('publisher') }}"
                                        placeholder="Ex: Microsoft"
                                    >
                                </div>
                                <div class="col-md-4">
                                    <label for="platform"
                                           class="form-label fw-medium">
                                        Plateforme
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="platform"
                                        name="platform"
                                        value="{{ old('platform') }}"
                                        placeholder="Ex: Windows"
                                    >
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="license_type"
                                           class="form-label fw-medium">
                                        Type de licence
                                    </label>
                                    <select class="form-select"
                                            id="license_type"
                                            name="license_type">
                                        <option value="">
                                            Choisir...
                                        </option>
                                        <option value="Perpétuelle"
                                            {{ old('license_type') ==
                                               'Perpétuelle'
                                               ? 'selected' : '' }}>
                                            Perpétuelle
                                        </option>
                                        <option value="Abonnement"
                                            {{ old('license_type') ==
                                               'Abonnement'
                                               ? 'selected' : '' }}>
                                            Abonnement
                                        </option>
                                        <option value="Open Source"
                                            {{ old('license_type') ==
                                               'Open Source'
                                               ? 'selected' : '' }}>
                                            Open Source
                                        </option>
                                        <option value="Essai"
                                            {{ old('license_type') ==
                                               'Essai'
                                               ? 'selected' : '' }}>
                                            Essai
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="release_date"
                                           class="form-label fw-medium">
                                        Date de sortie
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        id="release_date"
                                        name="release_date"
                                        value="{{ old('release_date') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Créer le produit
                            </button>
                            <a href="{{ route('products.index') }}"
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

@push('scripts')
<script>
    // Show/hide hardware or software fields based on type selection
    const hardwareFields = document.getElementById('hardware_fields');
    const softwareFields = document.getElementById('software_fields');
    const typeInputs = document.querySelectorAll('input[name="type"]');

    function toggleFields() {
        const selected = document.querySelector(
            'input[name="type"]:checked'
        )?.value;
        hardwareFields.style.display =
            selected === 'hardware' ? 'block' : 'none';
        softwareFields.style.display =
            selected === 'software' ? 'block' : 'none';
    }

    typeInputs.forEach(input => {
        input.addEventListener('change', toggleFields);
    });

    // Run on page load to restore state after validation failure
    toggleFields();
</script>
@endpush