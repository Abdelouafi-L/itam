@extends('layouts.app')

@section('title', 'Nouveau fournisseur')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('fournisseurs.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Nouveau fournisseur
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
                          action="{{ route('fournisseurs.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-medium">
                                    Nom du fournisseur
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('nom') is-invalid @enderror"
                                    name="nom"
                                    value="{{ old('nom') }}"
                                    placeholder="Ex: Dell Technologies"
                                    autofocus required>
                                @error('nom')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    N° TVA
                                </label>
                                <input type="text"
                                    class="form-control"
                                    name="numero_tva"
                                    value="{{ old('numero_tva') }}"
                                    placeholder="Ex: MA-12345678">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Email
                                </label>
                                <input type="email"
                                    class="form-control
                                           @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="contact@fournisseur.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Téléphone
                                </label>
                                <input type="text"
                                    class="form-control"
                                    name="telephone"
                                    value="{{ old('telephone') }}"
                                    placeholder="Ex: 05XX-XXXXXX">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Nom du contact
                                </label>
                                <input type="text"
                                    class="form-control"
                                    name="contact_nom"
                                    value="{{ old('contact_nom') }}"
                                    placeholder="Ex: Ahmed Benali">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Site web
                                </label>
                                <input type="url"
                                    class="form-control
                                           @error('site_web') is-invalid @enderror"
                                    name="site_web"
                                    value="{{ old('site_web') }}"
                                    placeholder="https://www.fournisseur.com">
                                @error('site_web')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Adresse
                            </label>
                            <textarea class="form-control"
                                name="adresse" rows="2"
                                placeholder="Adresse complète...">{{ old('adresse') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Créer le fournisseur
                            </button>
                            <a href="{{ route('fournisseurs.index') }}"
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