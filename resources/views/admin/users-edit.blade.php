@extends('layouts.app')

@section('title', 'Modifier — ' . $user->full_name)

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
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier — {{ $user->full_name }}
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
                          action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

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
                                    value="{{ old('first_name',
                                        $user->first_name) }}"
                                    required>
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
                                    value="{{ old('last_name',
                                        $user->last_name) }}"
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
                                    value="{{ old('email',
                                        $user->email) }}"
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
                                    value="{{ old('phone',
                                        $user->employee->phone) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Département
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        name="department_id" required>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ old('department_id',
                                               $user->employee
                                               ->department_id) ==
                                               $dept->id
                                               ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Rôle
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select"
                                        name="role_id" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id',
                                            $user->roles->first()?->id) ==
                                            $role->id
                                            ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Statut
                                </label>
                                <select class="form-select"
                                        name="is_active">
                                    <option value="1"
                                        {{ old('is_active',
                                           $user->is_active) == 1
                                           ? 'selected' : '' }}>
                                        Actif
                                    </option>
                                    <option value="0"
                                        {{ old('is_active',
                                           $user->is_active) == 0
                                           ? 'selected' : '' }}>
                                        Inactif
                                    </option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Changer le mot de passe
                            <span class="text-muted fw-normal">
                                (laisser vide pour ne pas modifier)
                            </span>
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Nouveau mot de passe
                                </label>
                                <input type="password"
                                    class="form-control
                                           @error('password')
                                           is-invalid @enderror"
                                    name="password"
                                    placeholder="Laisser vide = inchangé">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Confirmer
                                </label>
                                <input type="password"
                                    class="form-control"
                                    name="password_confirmation"
                                    placeholder="Confirmer le nouveau mot de passe">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
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