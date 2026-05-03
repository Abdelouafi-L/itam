@extends('layouts.app')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('categories.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Nouvelle catégorie
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
                          action="{{ route('categories.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name"
                                   class="form-label fw-medium">
                                Nom de la catégorie
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control
                                       @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Ex: Ordinateurs portables"
                                autofocus
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description"
                                   class="form-label fw-medium">
                                Description
                                <span class="text-muted small">
                                    (optionnel)
                                </span>
                            </label>
                            <textarea
                                class="form-control
                                       @error('description')
                                       is-invalid @enderror"
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Description de la catégorie..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Créer la catégorie
                            </button>
                            <a href="{{ route('categories.index') }}"
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