@extends('layouts.app')

@section('title', 'Produits')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-box-seam me-2 text-primary"></i>
                Produits
            </h2>
            <p class="text-muted small mb-0">
                {{ $products->total() }} produit(s)
                @if(request('search') || request('category_id') || request('type'))
                    <span class="text-warning">— filtrés</span>
                @endif
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
        <a href="{{ route('products.create') }}"
        class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouveau produit
        </a>
        @endif
    </div>

    {{-- Search & Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('products.index') }}"
                  class="row g-3">

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher par nom, marque, modèle..."
                            autofocus
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <select class="form-select"
                            name="category_id">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') ==
                                   $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select" name="type">
                        <option value="">Tous les types</option>
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

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Rechercher
                    </button>
                    @if(request('search') || request('category_id')
                        || request('type'))
                    <a href="{{ route('products.index') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-x me-1"></i>
                        Effacer
                    </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- Table --}}
    @if($products->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucun produit trouvé.
            <a href="{{ route('products.create') }}">
                Ajouter le premier produit
            </a>
        </div>
    @else
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
                            <th class="text-center">Stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="text-muted small">
                                {{ $product->id }}
                            </td>
                            <td class="fw-medium">
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-decoration-none">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                {{ $product->category->name ?? '—' }}
                            </td>
                            <td>
                                @if($product->hardware)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-cpu me-1"></i>
                                        Hardware
                                    </span>
                                @elseif($product->software)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-code-square me-1"></i>
                                        Software
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $product->brand ?? '—' }}
                                {{ $product->model ? '/ ' . $product->model : '' }}
                            </td>
                            <td class="text-center">
                                @if($product->stock)
                                    <span class="badge {{
                                        $product->stock->quantity_available > 0
                                        ? 'bg-success' : 'bg-secondary'
                                    }}">
                                        {{ $product->stock->quantity_available }}
                                        / {{ $product->stock->quantity_total }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">

                                    {{-- Show button — everyone --}}
                                    <a href="{{ route('products.show', $product) }}"
                                    class="btn btn-outline-info"
                                    title="Détails">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit + Delete — Admin + Tech only --}}
                                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
                                    <a href="{{ route('products.edit', $product) }}"
                                    class="btn btn-outline-primary"
                                    title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Supprimer ce produit ?')"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection