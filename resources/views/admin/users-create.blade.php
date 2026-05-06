@extends('layouts.app')

@section('title', 'Nouveau compte utilisateur')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('users.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-person-plus me-2 text-primary"></i>
                    Nouveau compte utilisateur
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
                          action="{{ route('users.store') }}">
                        @csrf

                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Informations personnelles
                        </h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Prénom
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('first_name')
                                           is-invalid @enderror"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    autofocus required>
                                @error('first_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Nom
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('last_name')
                                           is-invalid @enderror"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    required>
                                @error('last_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Email
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                    class="form-control
                                           @error('email')
                                           is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required>
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
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="06XXXXXXXX">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Département
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('department_id')
                                           is-invalid @enderror"
                                    name="department_id" required>
                                    <option value="">
                                        Choisir...
                                    </option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ old('department_id') ==
                                               $dept->id
                                               ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Rôle
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('role_id')
                                           is-invalid @enderror"
                                    name="role_id" required>
                                    <option value="">
                                        Choisir...
                                    </option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') ==
                                               $role->id
                                               ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Mot de passe
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Mot de passe
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                    class="form-control
                                           @error('password')
                                           is-invalid @enderror"
                                    name="password"
                                    placeholder="Minimum 8 caractères"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Confirmer
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                    class="form-control"
                                    name="password_confirmation"
                                    placeholder="Répéter le mot de passe"
                                    required>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Créer le compte
                            </button>
                            <a href="{{ route('users.index') }}"
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