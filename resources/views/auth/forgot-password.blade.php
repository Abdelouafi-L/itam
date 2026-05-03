@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock fs-1 text-primary"></i>
                        <h4 class="fw-bold mt-2">Mot de passe oublié ?</h4>
                        <p class="text-muted small">
                            Entrez votre email pour recevoir un lien 
                            de réinitialisation.
                        </p>
                    </div>

                    {{-- Success message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

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
                          action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">
                                Adresse email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    class="form-control @error('email') 
                                           is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="exemple@techcorp.ma"
                                    autofocus
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" 
                                    class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>
                                Envoyer le lien
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