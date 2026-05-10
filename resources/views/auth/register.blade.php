@extends('layouts.app')

@section('title', 'Créer un compte')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-pc-display fs-1 text-primary"></i>
                        <h4 class="fw-bold mt-2">ITAM</h4>
                        <p class="text-muted small">
                            Créer un compte administrateur
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

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col">
                                <label for="first_name" class="form-label fw-medium">
                                    Prénom
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    placeholder="Abdelouafi"
                                    autofocus
                                    required
                                >
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="last_name" class="form-label fw-medium">
                                    Nom
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    placeholder="Louardi"
                                    required
                                >
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">
                                Mot de passe
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Minimum 8 caractères"
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
                                        role="progressbar" style="width:0%; transition: all 0.3s">
                                    </div>
                                </div>
                                <small id="strengthText" class="mt-1 d-block"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">
                                Confirmer le mot de passe
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Répétez le mot de passe"
                                    required
                                >
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>
                                Créer mon compte
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="text-decoration-none">
                    Se connecter
                </a>
            </p>

        </div>
    </div>
</div>
@push('scripts')
<script>
const passwordInput    = document.getElementById('password');
const strengthBar      = document.getElementById('strengthBar');
const strengthText     = document.getElementById('strengthText');
const strengthContainer = document.getElementById('strengthContainer');

passwordInput.addEventListener('input', function () {
    const val     = this.value;
    const len     = val.length;

    if (len === 0) {
        strengthContainer.style.display = 'none';
        return;
    }

    strengthContainer.style.display = 'block';

    const hasLetters = /[a-zA-Z]/.test(val);
    const hasNumbers = /[0-9]/.test(val);
    const isAlphanumeric = hasLetters && hasNumbers;

    let strength  = 0;
    let color     = '';
    let text      = '';
    let width     = '';

    if (len < 8) {
        // Red — too short
        strength = 1;
        color    = 'bg-danger';
        text     = '❌ Trop court — minimum 8 caractères';
        width    = '33%';
    } else if (len >= 8 && !isAlphanumeric) {
        // Orange — long enough but not mixed
        strength = 2;
        color    = 'bg-warning';
        text     = '⚠️ Moyen — ajoutez des chiffres et des lettres';
        width    = '66%';
    } else if (len >= 8 && isAlphanumeric) {
        // Green — strong
        strength = 3;
        color    = 'bg-success';
        text     = '✅ Fort — mot de passe sécurisé';
        width    = '100%';
    }

    // Update bar
    strengthBar.className = 'progress-bar ' + color;
    strengthBar.style.width = width;
    strengthText.className  = 'mt-1 d-block ' + (
        strength === 1 ? 'text-danger' :
        strength === 2 ? 'text-warning' : 'text-success'
    );
    strengthText.textContent = text;
});
</script>
@endpush
@endsection