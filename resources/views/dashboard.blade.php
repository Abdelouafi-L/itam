@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-speedometer2 me-2 text-primary"></i>
                Tableau de bord
            </h2>
            <p class="text-muted">
                Bienvenue, {{ Auth::user()->full_name }} —
                {{ Auth::user()->role->name }}
            </p>
            <hr>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-pc-display fs-1 text-primary"></i>
                <h5 class="fw-bold mt-2">Équipements</h5>
                <p class="text-muted small">Gérer le parc matériel</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-people fs-1 text-success"></i>
                <h5 class="fw-bold mt-2">Employés</h5>
                <p class="text-muted small">Gérer les affectations</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-key fs-1 text-warning"></i>
                <h5 class="fw-bold mt-2">Licences</h5>
                <p class="text-muted small">Suivre les licences logicielles</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-wrench fs-1 text-danger"></i>
                <h5 class="fw-bold mt-2">Maintenance</h5>
                <p class="text-muted small">Historique des réparations</p>
            </div>
        </div>
    </div>
</div>
@endsection