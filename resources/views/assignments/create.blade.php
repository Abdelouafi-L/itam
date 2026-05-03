@extends('layouts.app')

@section('title', 'Nouvelle affectation')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('assignments.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Nouvelle affectation
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
                  action="{{ route('assignments.store') }}"
                  id="assignmentForm">
                @csrf

                {{-- Employee selection --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-bold">
                        <i class="bi bi-person me-2"></i>
                        Employé bénéficiaire
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="employee_id"
                                       class="form-label fw-medium">
                                    Sélectionner l'employé
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select
                                           @error('employee_id')
                                           is-invalid @enderror"
                                    id="employee_id"
                                    name="employee_id"
                                    required
                                >
                                    <option value="">
                                        Choisir un employé...
                                    </option>
                                    @foreach($employees as $employee)
                                        <option
                                            value="{{ $employee->id }}"
                                            {{ old('employee_id') ==
                                               $employee->id
                                               ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                            — {{ $employee->department
                                                 ->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="notes"
                                   class="form-label fw-medium">
                                Notes
                            </label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="2"
                                placeholder="Notes optionnelles..."
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Product lines --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-bold
                                d-flex justify-content-between
                                align-items-center">
                        <span>
                            <i class="bi bi-box-seam me-2"></i>
                            Produits à affecter
                        </span>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="addLine">
                            <i class="bi bi-plus me-1"></i>
                            Ajouter un produit
                        </button>
                    </div>
                    <div class="card-body">

                        <div id="productLines">
                            {{-- Lines added dynamically by JS --}}
                        </div>

                        <div id="noLines" class="text-muted text-center
                                                  py-3">
                            Cliquez sur "Ajouter un produit" pour
                            commencer.
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Créer l'affectation
                    </button>
                    <a href="{{ route('assignments.index') }}"
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
// Pass products data from PHP to JavaScript
const products = {!! $productsJson !!};

let lineIndex = 0;

document.getElementById('addLine').addEventListener('click', function() {
    addProductLine();
});

function addProductLine() {
    const container = document.getElementById('productLines');
    const noLines   = document.getElementById('noLines');
    noLines.style.display = 'none';

    const index = lineIndex++;
    const div   = document.createElement('div');
    div.className = 'border rounded p-3 mb-3 bg-light';
    div.id = `line_${index}`;

    // Build product options
    let options = '<option value="">Choisir un produit...</option>';
    products.forEach(p => {
        options += `<option value="${p.id}">
            ${p.name} ${p.brand ? '(' + p.brand + ')' : ''}
            — Disponible: ${p.available}
        </option>`;
    });

    div.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-medium small">
                    Produit <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-sm"
                        name="products[${index}][id]"
                        required>
                    ${options}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">
                    Quantité <span class="text-danger">*</span>
                </label>
                <input type="number"
                       class="form-control form-control-sm"
                       name="products[${index}][quantity]"
                       value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">
                    N° Série
                </label>
                <input type="text"
                       class="form-control form-control-sm"
                       name="products[${index}][serial_number]"
                       placeholder="Optionnel">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">
                    Asset Tag
                </label>
                <input type="text"
                       class="form-control form-control-sm"
                       name="products[${index}][asset_tag]"
                       placeholder="Optionnel">
            </div>
            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100"
                        onclick="removeLine(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(div);
}

function removeLine(index) {
    const line = document.getElementById(`line_${index}`);
    if (line) line.remove();

    const container = document.getElementById('productLines');
    if (container.children.length === 0) {
        document.getElementById('noLines').style.display = 'block';
    }
}

// Add first line automatically
addProductLine();
</script>
@endpush