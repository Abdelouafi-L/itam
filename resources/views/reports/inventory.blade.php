@extends('layouts.app')

@section('title', 'Rapport — Inventaire des actifs')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-clipboard-data me-2 text-primary"></i>
                Inventaire des actifs — RF-25
            </h2>
            <p class="text-muted small mb-0">
                {{ $summary['total'] }} produit(s) —
                {{ $summary['available'] }} disponible(s) —
                {{ $summary['assigned'] }} affecté(s)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rapports.inventory.pdf') }}"
               class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('rapports.inventory.csv') }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-spreadsheet me-1"></i> CSV
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('rapports.inventory') }}"
                  class="row g-3">

                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Catégorie
                    </label>
                    <select class="form-select form-select-sm"
                            name="category_id">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') ==
                                   $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Type
                    </label>
                    <select class="form-select form-select-sm"
                            name="type">
                        <option value="">Tous</option>
                        <option value="hardware"
                            {{ request('type') == 'hardware'
                               ? 'selected' : '' }}>
                            Hardware
                        </option>
                        <option value="software"
                            {{ request('type') == 'software'
                               ? 'selected' : '' }}>
                            Software
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-medium">
                        Disponibilité
                    </label>
                    <select class="form-select form-select-sm"
                            name="availability">
                        <option value="">Tous</option>
                        <option value="available"
                            {{ request('availability') == 'available'
                               ? 'selected' : '' }}>
                            Disponible
                        </option>
                        <option value="out"
                            {{ request('availability') == 'out'
                               ? 'selected' : '' }}>
                            Épuisé
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('rapports.inventory') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Réinitialiser
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Type</th>
                        <th>Marque / Modèle</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Disponible</th>
                        <th class="text-center">Affecté</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-muted small">
                            {{ $product->id }}
                        </td>
                        <td class="fw-medium">
                            <a href="{{ route('products.show',
                                       $product) }}"
                               class="text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="small text-muted">
                            {{ $product->category->name ?? '—' }}
                        </td>
                        <td>
                            @if($product->hardware)
                                <span class="badge bg-danger">
                                    Hardware
                                </span>
                            @else
                                <span class="badge bg-warning
                                             text-dark">
                                    Software
                                </span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $product->brand ?? '—' }}
                            {{ $product->model
                               ? '/ ' . $product->model : '' }}
                        </td>
                        <td class="text-center">
                            {{ $product->stock?->quantity_total ?? 0 }}
                        </td>
                        <td class="text-center">
                            <span class="badge {{
                                ($product->stock?->quantity_available
                                 ?? 0) > 0
                                ? 'bg-success' : 'bg-secondary'
                            }}">
                                {{ $product->stock?->quantity_available
                                   ?? 0 }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{ $product->stock?->quantity_assigned ?? 0 }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center
                                               text-muted py-4">
                            Aucun produit trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">
                            Totaux:
                        </td>
                        <td class="text-center">
                            {{ $products->sum(fn($p) =>
                               $p->stock?->quantity_total ?? 0) }}
                        </td>
                        <td class="text-center">
                            {{ $summary['available'] }}
                        </td>
                        <td class="text-center">
                            {{ $summary['assigned'] }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection