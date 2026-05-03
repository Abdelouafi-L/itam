@extends('layouts.app')

@section('title', 'Catégories')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-tags me-2 text-primary"></i>
                Catégories
            </h2>
            <p class="text-muted small mb-0">
                {{ $categories->count() }} catégorie(s) au total
            </p>
        </div>
        <a href="{{ route('categories.create') }}" 
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouvelle catégorie
        </a>
    </div>

    {{-- Table --}}
    @if($categories->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune catégorie trouvée. 
            <a href="{{ route('categories.create') }}">
                Créer la première catégorie
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
                            <th>Description</th>
                            <th class="text-center">Produits</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td class="text-muted small">
                                {{ $category->id }}
                            </td>
                            <td class="fw-medium">
                                {{ $category->name }}
                            </td>
                            <td class="text-muted small">
                                {{ $category->description ?? '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">
                                    {{ $category->products_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">

                                    {{-- Edit --}}
                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="btn btn-outline-primary"
                                       title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form
                                        method="POST"
                                        action="{{ route('categories.destroy', $category) }}"
                                        onsubmit="return confirm('Supprimer cette catégorie ?')"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger"
                                                title="Supprimer"
                                                {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection