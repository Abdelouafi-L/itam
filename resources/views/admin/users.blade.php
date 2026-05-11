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
                {{ $users->count() }} compte(s) utilisateur(s)
            </p>
        </div>
        <a href="{{ route('users.create') }}"
           class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>
            Nouveau compte
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Département</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="{{ !$user->is_active
                                  ? 'table-secondary' : '' }}">
                        <td class="fw-medium">
                            {{ $user->full_name }}
                            @if($user->id === Auth::id())
                                <span class="badge bg-info ms-1">
                                    Vous
                                </span>
                            @endif
                        </td>
                        <td class="small">
                            {{ $user->email }}
                        </td>
                        <td class="small text-muted">
                            {{ $user->employee->department->name
                               ?? '—' }}
                        </td>
                        <td>
                            <span class="badge {{
                                $user->getRoleNames()->first() === 'Administrateur'
                                ? 'bg-danger'
                                : ($user->getRoleNames()->first() === 'Technicien'
                                ? 'bg-primary'
                                : ($user->getRoleNames()->first() === 'Manager'
                                    ? 'bg-warning text-dark'
                                    : 'bg-secondary'))
                            }}">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{
                                $user->is_active
                                ? 'bg-success' : 'bg-secondary'
                            }}">
                                {{ $user->is_active
                                   ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit',
                                           $user) }}"
                                   class="btn btn-outline-primary"
                                   title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                <form method="POST"
                                      action="{{ route('users.destroy',
                                                 $user) }}"
                                      onsubmit="return confirm(
                                          '{{ $user->is_active
                                             ? 'Désactiver' : 'Activer' }}
                                           ce compte ?')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn {{
                                                $user->is_active
                                                ? 'btn-outline-warning'
                                                : 'btn-outline-success'
                                            }}"
                                            title="{{
                                                $user->is_active
                                                ? 'Désactiver'
                                                : 'Activer'
                                            }}">
                                        <i class="bi {{
                                            $user->is_active
                                            ? 'bi-person-slash'
                                            : 'bi-person-check'
                                        }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6"
                            class="text-center text-muted py-4">
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection