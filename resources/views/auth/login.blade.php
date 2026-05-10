@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            {{-- Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    {{-- Header --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-pc-display fs-1 text-primary"></i>
                        <h4 class="fw-bold mt-2">ITAM</h4>
                        <p class="text-muted small">
                            Système de Gestion de Parc Informatique
                        </p>
                    </div>

                    {{-- Validation errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Login form --}}
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">
                                Adresse email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="exemple@techcorp.ma"
                                    autofocus
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label fw-medium">
                                    Mot de passe
                                </label>
                                <a href="{{ route('password.request') }}"
                                class="small text-decoration-none">
                                    Mot de passe oublié ?
                                </a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password strength indicator --}}
                            <div class="mt-2" id="strengthContainer" style="display:none">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" id="strengthBar"
                                        role="progressbar"
                                        style="width:0%; transition: all 0.3s">
                                    </div>
                                </div>
                                <small id="strengthText" class="mt-1 d-block"></small>
                            </div>
                        </div>

                        {{-- Remember me --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="remember" 
                                    name="remember"
                                >
                                <label class="form-check-label small" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Se connecter
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {{-- Register link --}}
            <p class="text-center text-muted small mt-3">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-decoration-none">
                    Créer un compte
                </a>
            </p>

        </div>
    </div>
</div>
@push('scripts')
<script>
const passwordInput     = document.getElementById('password');
const strengthBar       = document.getElementById('strengthBar');
const strengthText      = document.getElementById('strengthText');
const strengthContainer = document.getElementById('strengthContainer');

passwordInput.addEventListener('input', function () {
    const val = this.value;
    const len = val.length;

    if (len === 0) {
        strengthContainer.style.display = 'none';
        return;
    }

    strengthContainer.style.display = 'block';

    const hasLetters     = /[a-zA-Z]/.test(val);
    const hasNumbers     = /[0-9]/.test(val);
    const isAlphanumeric = hasLetters && hasNumbers;

    let color = '';
    let text  = '';
    let width = '';

    if (len < 8) {
        color = 'bg-danger';
        text  = '❌ Trop court — minimum 8 caractères';
        width = '33%';
    } else if (len >= 8 && !isAlphanumeric) {
        color = 'bg-warning';
        text  = '⚠️ Moyen — ajoutez des chiffres et des lettres';
        width = '66%';
    } else {
        color = 'bg-success';
        text  = '✅ Fort — mot de passe sécurisé';
        width = '100%';
    }

    strengthBar.className    = 'progress-bar ' + color;
    strengthBar.style.width  = width;
    strengthText.className   = 'mt-1 d-block ' + (
        width === '33%' ? 'text-danger' :
        width === '66%' ? 'text-warning' : 'text-success'
    );
    strengthText.textContent = text;
});
</script>
@endpush
@endsection