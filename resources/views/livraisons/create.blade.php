@extends('layouts.app')

@section('title', 'Nouvelle livraison')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ !empty($selectedFournisseurId) 
                            ? route('fournisseurs.show', $selectedFournisseurId) 
                            : route('livraisons.index') }}"
                    class="btn btn-outline-secondary btn-sm me-3">
                        <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Nouvelle livraison
                </h2>
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
                  action="{{ route('livraisons.store') }}"
                  id="livraisonForm">
                @csrf

                {{-- Header info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-bold">
                        <i class="bi bi-file-text me-2"></i>
                        Informations de livraison
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Référence interne
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('reference_interne')
                                           is-invalid @enderror"
                                    name="reference_interne"
                                    value="{{ old('reference_interne',
                                        $reference) }}"
                                    required>
                                @error('reference_interne')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    Généré automatiquement
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Bon de livraison
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control
                                           @error('bon_de_livraison')
                                           is-invalid @enderror"
                                    name="bon_de_livraison"
                                    value="{{ old('bon_de_livraison') }}"
                                    placeholder="Ex: BL-SUP-78542"
                                    required>
                                @error('bon_de_livraison')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    N° du fournisseur
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">
                                    Date de livraison
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control
                                           @error('date_livraison')
                                           is-invalid @enderror"
                                    name="date_livraison"
                                    value="{{ old('date_livraison',
                                        now()->format('Y-m-d')) }}"
                                    required>
                                @error('date_livraison')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Fournisseur
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                        @error('fournisseur_id')
                                        is-invalid @enderror"
                                    name="fournisseur_id" required>
                                    <option value="">
                                        Choisir un fournisseur...
                                    </option>
                                    @foreach($fournisseurs as $f)
                                        <option value="{{ $f->id }}"
                                            {{ old('fournisseur_id', $selectedFournisseurId) == $f->id 
                                                ? 'selected' : '' }}>
                                            {{ $f->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Signataire
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('signataire_id')
                                           is-invalid @enderror"
                                    name="signataire_id" required>
                                    <option value="">
                                        Qui a signé la livraison ?
                                    </option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('signataire_id') ==
                                               $employee->id
                                               ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('signataire_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">
                                Notes
                            </label>
                            <textarea class="form-control"
                                name="notes" rows="2"
                                placeholder="Notes optionnelles...">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Product lines --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-bold
                                d-flex justify-content-between
                                align-items-center">
                        <span>
                            <i class="bi bi-list-ul me-2"></i>
                            Détails de livraison — RF-34
                        </span>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="addLine">
                            <i class="bi bi-plus me-1"></i>
                            Ajouter un produit
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productLines"></div>
                        <div id="noLines"
                             class="text-muted text-center py-3">
                            Cliquez sur "Ajouter un produit"
                            pour commencer.
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Créer la livraison
                    </button>
                    <a href="{{ !empty($selectedFournisseurId) 
                                ? route('fournisseurs.show', $selectedFournisseurId) 
                                : route('livraisons.index') }}"
                        class="btn btn-outline-secondary">
                            Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const products = {!! $productsJson ?? '[]' !!};
let lineIndex  = 0;

document.getElementById('addLine')
        .addEventListener('click', addLine);

function addLine() {
    const container = document.getElementById('productLines');
    const noLines   = document.getElementById('noLines');
    noLines.style.display = 'none';

    const i   = lineIndex++;
    const div = document.createElement('div');
    div.className = 'border rounded p-3 mb-3 bg-light';
    div.id = `line_${i}`;

    let opts = '<option value="">Choisir un produit...</option>';
    products.forEach(p => {
        opts += `<option value="${p.id}">
            ${p.name} ${p.brand ? '(' + p.brand + ')' : ''}
        </option>`;
    });

    div.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-medium small">
                    Produit <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-sm"
                        name="products[${i}][id]" required>
                    ${opts}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">
                    Quantité <span class="text-danger">*</span>
                </label>
                <input type="number"
                       class="form-control form-control-sm"
                       name="products[${i}][quantite]"
                       value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small">
                    Prix unitaire (MAD)
                </label>
                <input type="number"
                       class="form-control form-control-sm"
                       name="products[${i}][prix_unitaire]"
                       step="0.01" min="0"
                       placeholder="0.00">
            </div>
            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100"
                        onclick="removeLine(${i})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;

    container.appendChild(div);
}

function removeLine(i) {
    const line = document.getElementById(`line_${i}`);
    if (line) line.remove();
    const container = document.getElementById('productLines');
    if (container.children.length === 0) {
        document.getElementById('noLines').style.display = 'block';
    }
}

addLine();
</script>
@endpush