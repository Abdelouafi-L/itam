@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-lock-fill fs-1 text-primary"></i>
                        <h4 class="fw-bold mt-2">Nouveau mot de passe</h4>
                        <p class="text-muted small">
                            Choisissez un nouveau mot de passe sécurisé.
                        </p>
                    </div>

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
                          action="{{ route('password.update') }}">
                        @csrf

                        {{-- Hidden fields — token and email --}}
                        <input type="hidden" name="token" 
                               value="{{ $token }}">
                        <input type="hidden" name="email" 
                               value="{{ request()->email }}">

                        {{-- New password --}}
                        <div class="mb-3">
                            <label for="password" 
                                   class="form-label fw-medium">
                                Nouveau mot de passe
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control 
                                           @error('password') 
                                           is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Minimum 8 caractères"
                                    autofocus
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" 
                                   class="form-label fw-medium">
                                Confirmer le mot de passe
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control
                                           @error('password_confirmation')
                                           is-invalid @enderror"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Répétez le mot de passe"
                                    required
                                >
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" 
                                    class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>
                                Réinitialiser le mot de passe
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                <a href="{{ route('login') }}" 
                   class="text-decoration-none">
                    ← Retour à la connexion
                </a>
            </p>

        </div>
    </div>
</div>
@endsection