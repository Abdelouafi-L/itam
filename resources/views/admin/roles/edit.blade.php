@extends('layouts.app')

@section('title', 'Modifier rôle — ' . $role->name)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('roles.index') }}"
                   class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-pencil me-2 text-primary"></i>
                    Modifier — {{ $role->name }} — RF-41/42
                </h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

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
                          action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Nom du rôle
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control
                                       @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', $role->name) }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <h6 class="fw-bold text-muted text-uppercase
                                   small mb-3">
                            Permissions — RF-42
                        </h6>

                        @foreach($permissionsByModule as $module => $perms)
                        <div class="mb-3">
                            <div class="d-flex align-items-center
                                        gap-2 mb-2">
                                <span class="badge bg-secondary">
                                    {{ strtoupper($module) }}
                                </span>
                                <button type="button"
                                        class="btn btn-link btn-sm p-0
                                               text-decoration-none
                                               select-all-btn"
                                        data-module="{{ $module }}">
                                    Tout sélectionner
                                </button>
                            </div>
                            <div class="row g-2">
                                @foreach($perms as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input
                                                   perm-{{ $module }}"
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            id="perm_{{ $permission->id }}"
                                            {{ in_array($permission->id,
                                               old('permissions',
                                                   $rolePermissions))
                                               ? 'checked' : '' }}>
                                        <label class="form-check-label
                                                      small"
                                               for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Enregistrer
                            </button>
                            <a href="{{ route('roles.index') }}"
                               class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.select-all-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const module = this.dataset.module;
        const boxes  = document.querySelectorAll('.perm-' + module);
        const allChecked = [...boxes].every(b => b.checked);
        boxes.forEach(b => b.checked = !allChecked);
        this.textContent = allChecked
            ? 'Tout sélectionner'
            : 'Tout désélectionner';
    });
});
</script>
@endpush