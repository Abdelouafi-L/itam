@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-people me-2 text-primary"></i>
                Gestion des utilisateurs
            </h2>
            <p class="text-muted small mb-0">
                Module en cours de développement
            </p>
        </div>
    </div>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Ce module sera disponible prochainement.
        Les utilisateurs peuvent être créés via la page
        <a href="{{ route('register') }}">d'inscription</a>.
    </div>
</div>
@endsection