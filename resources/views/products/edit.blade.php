@extends('layouts.app')

@section('title', 'Modifier — ' . $product->name)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier — {{ $product->name }}
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
                          action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')

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
                                    value="{{ old('name', $product->name) }}"
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
                                    class="form-select"
                                    id="category_id"
                                    name="category_id"
                                    required
                                >
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id',
                                               $product->category_id)
                                               == $category->id
                                               ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Marque
                                </label>
                                <input type="text" class="form-control"
                                    name="brand"
                                    value="{{ old('brand', $product->brand) }}"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Modèle
                                </label>
                                <input type="text" class="form-control"
                                    name="model"
                                    value="{{ old('model', $product->model) }}"
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                Description
                            </label>
                            <textarea class="form-control"
                                name="description" rows="2"
                            >{{ old('description', $product->description) }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- Type indicator — cannot change type after creation --}}
                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Type de produit
                        </h6>

                        <div class="alert alert-light border mb-4">
                            @if($product->hardware)
                                <i class="bi bi-cpu me-2 text-danger"></i>
                                <strong>Hardware</strong> —
                                Le type ne peut pas être modifié.
                            @elseif($product->software)
                                <i class="bi bi-code-square me-2
                                          text-warning"></i>
                                <strong>Software</strong> —
                                Le type ne peut pas être modifié.
                            @endif
                        </div>

                        {{-- Hardware fields --}}
                        @if($product->hardware)
                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Détails matériel
                        </h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    État
                                </label>
                                <select class="form-select"
                                        name="condition">
                                    @foreach(['Neuf','Bon','Usagé',
                                              'Endommagé'] as $c)
                                        <option value="{{ $c }}"
                                            {{ old('condition',
                                               $product->hardware->condition)
                                               == $c ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Date d'achat
                                </label>
                                <input type="date" class="form-control"
                                    name="purchase_date"
                                    value="{{ old('purchase_date',
                                        $product->hardware->purchase_date
                                        ?->format('Y-m-d')) }}"
                                >
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Fin de garantie
                                </label>
                                <input type="date" class="form-control"
                                    name="warranty_date"
                                    value="{{ old('warranty_date',
                                        $product->hardware->warranty_date
                                        ?->format('Y-m-d')) }}"
                                >
                            </div>
                        </div>
                        @endif

                        {{-- Software fields --}}
                        @if($product->software)
                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Détails logiciel
                        </h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Version
                                </label>
                                <input type="text" class="form-control"
                                    name="version"
                                    value="{{ old('version',
                                        $product->software->version) }}"
                                >
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Éditeur
                                </label>
                                <input type="text" class="form-control"
                                    name="publisher"
                                    value="{{ old('publisher',
                                        $product->software->publisher) }}"
                                >
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Plateforme
                                </label>
                                <input type="text" class="form-control"
                                    name="platform"
                                    value="{{ old('platform',
                                        $product->software->platform) }}"
                                >
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Type de licence
                                </label>
                                <select class="form-select"
                                        name="license_type">
                                    <option value="">Choisir...</option>
                                    @foreach(['Perpétuelle','Abonnement',
                                              'Open Source','Essai'] as $lt)
                                        <option value="{{ $lt }}"
                                            {{ old('license_type',
                                               $product->software->license_type)
                                               == $lt ? 'selected' : '' }}>
                                            {{ $lt }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Date de sortie
                                </label>
                                <input type="date" class="form-control"
                                    name="release_date"
                                    value="{{ old('release_date',
                                        $product->software->release_date
                                        ?->format('Y-m-d')) }}"
                                >
                            </div>
                        </div>
                        @endif

                        <hr class="my-4">

                        {{-- Stock — read only, managed by Livraisons --}}
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">
                            Stock
                        </h6>
                        <div class="row mb-4">
                            @if($product->stock)
                            <div class="col-md-3">
                                <label class="form-label fw-medium">Total</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $product->stock->quantity_total }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium">Disponible</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $product->stock->quantity_available }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium">Assigné</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $product->stock->quantity_assigned }}" disabled>
                            </div>
                            @endif
                            <div class="col-12 mt-2">
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Le stock est géré automatiquement par les livraisons — non modifiable ici.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
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