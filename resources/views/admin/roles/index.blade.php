@extends('layouts.app')

@section('title', 'Gestion des rôles')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-shield-check me-2 text-primary"></i>
                Rôles & Permissions — RF-40 à RF-44
            </h2>
            <p class="text-muted small mb-0">
                {{ $roles->count() }} rôle(s) dans le système
            </p>
        </div>
        <a href="{{ route('roles.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nouveau rôle
        </a>
    </div>

    {{-- Permission Matrix — RF-43 --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-bold">
            <i class="bi bi-grid-3x3 me-2"></i>
            Matrice des permissions — RF-43
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Permission</th>
                        @foreach($roles as $role)
                            <th class="text-center">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissionsByModule as $module => $perms)
                        <tr class="table-light">
                            <td colspan="{{ $roles->count() + 1 }}"
                                class="fw-bold text-uppercase small
                                       text-muted">
                                {{ $module }}
                            </td>
                        </tr>
                        @foreach($perms as $permission)
                        <tr>
                            <td class="small">
                                {{ $permission->name }}
                            </td>
                            @foreach($roles as $role)
                                <td class="text-center">
                                    @if($role->hasPermissionTo($permission))
                                        <i class="bi bi-check-circle-fill
                                                  text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle
                                                  text-muted"></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Roles list --}}
    <div class="row g-4">
        @foreach($roles as $role)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent
                             d-flex justify-content-between
                             align-items-center">
                    <span class="fw-bold">
                        <i class="bi bi-shield me-2"></i>
                        {{ $role->name }}
                    </span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('roles.edit', $role) }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>
                            Modifier
                        </a>
                        <form method="POST"
                              action="{{ route('roles.destroy', $role) }}"
                              onsubmit="return confirm(
                                  'Supprimer le rôle {{ $role->name }} ?')"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        {{ $role->permissions->count() }} permission(s)
                        — {{ $role->users()->count() }} utilisateur(s)
                    </p>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($role->permissions->sortBy('name')
                                 as $permission)
                            <span class="badge bg-light text-dark
                                         border small">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-muted small">
                                Aucune permission assignée
                            </span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection