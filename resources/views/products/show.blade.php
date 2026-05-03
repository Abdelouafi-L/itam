@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('products.index') }}"
           class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">{{ $product->name }}</h2>
            <p class="text-muted small mb-0">
                {{ $product->category->name ?? '—' }} —
                @if($product->hardware)
                    <span class="badge bg-danger">Hardware</span>
                @elseif($product->software)
                    <span class="badge bg-warning text-dark">Software</span>
                @endif
            </p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('products.edit', $product) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Product info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations générales
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Nom</td>
                            <td class="fw-medium">{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Marque</td>
                            <td>{{ $product->brand ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modèle</td>
                            <td>{{ $product->model ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catégorie</td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>{{ $product->description ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Stock info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-boxes me-2"></i>Stock
                </div>
                <div class="card-body">
                    @if($product->stock)
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold text-primary">
                                    {{ $product->stock->quantity_total }}
                                </div>
                                <div class="small text-muted">Total</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold text-success">
                                    {{ $product->stock->quantity_available }}
                                </div>
                                <div class="small text-muted">
                                    Disponible
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-3 fw-bold text-warning">
                                    {{ $product->stock->quantity_assigned }}
                                </div>
                                <div class="small text-muted">Affecté</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Hardware or Software details --}}
        @if($product->hardware)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-cpu me-2 text-danger"></i>
                    Détails matériel
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">État</p>
                            <p class="fw-medium">
                                {{ $product->hardware->condition ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">
                                Date d'achat
                            </p>
                            <p class="fw-medium">
                                {{ $product->hardware->purchase_date
                                   ?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">
                                Fin de garantie
                            </p>
                            <p class="fw-medium">
                                {{ $product->hardware->warranty_date
                                   ?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($product->software)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-code-square me-2 text-warning"></i>
                    Détails logiciel
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Version</p>
                            <p class="fw-medium">
                                {{ $product->software->version ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Éditeur</p>
                            <p class="fw-medium">
                                {{ $product->software->publisher ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">Plateforme</p>
                            <p class="fw-medium">
                                {{ $product->software->platform ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small mb-1">
                                Type de licence
                            </p>
                            <p class="fw-medium">
                                {{ $product->software->license_type ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection