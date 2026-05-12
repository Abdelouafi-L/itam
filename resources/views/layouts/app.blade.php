<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ITAM') }} — @yield('title', 'Accueil')</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" 
          rel="stylesheet">

    {{-- Page specific styles --}}
    @stack('styles')

    {{-- Page specific styles --}}
    @stack('styles')
</head>
<body class="bg-light">

    {{-- Navigation bar (only shown when logged in) --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            {{-- Brand --}}
            <a class="navbar-brand fw-bold"
            href="{{ route('dashboard') }}">
                <i class="bi bi-pc-display me-2"></i>ITAM
            </a>

            {{-- Mobile toggle --}}
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav links --}}
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">

                    {{-- 1. Dashboard — everyone --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Tableau de bord
                        </a>
                    </li>

                    {{-- 2. Catalogue — Admin + Tech --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('licenses.*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-box-seam me-1"></i>Catalogue
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                                href="{{ route('categories.index') }}">
                                    <i class="bi bi-tags me-2 text-muted"></i>Catégories
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('products.*') ? 'active' : '' }}"
                                href="{{ route('products.index') }}">
                                    <i class="bi bi-box me-2 text-muted"></i>Produits
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('licenses.*') ? 'active' : '' }}"
                                href="{{ route('licenses.index') }}">
                                    <i class="bi bi-key me-2 text-muted"></i>Licences
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}"
                                href="{{ route('fournisseurs.index') }}">
                                    <i class="bi bi-truck me-2 text-muted"></i>
                                    Fournisseurs
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('livraisons.*') ? 'active' : '' }}"
                                href="{{ route('livraisons.index') }}">
                                    <i class="bi bi-box-arrow-in-down me-2 text-muted"></i>
                                    Livraisons
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    {{-- 3. Affectations — Admin + Tech + Manager --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien() || Auth::user()->isManager())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}"
                        href="{{ route('assignments.index') }}">
                            <i class="bi bi-arrow-left-right me-1"></i>Affectations
                        </a>
                    </li>
                    @endif

                    {{-- 3. Mes affectations — Employé only --}}
                    @if(Auth::user()->hasRole('Employé'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}"
                        href="{{ route('assignments.index') }}">
                            <i class="bi bi-arrow-left-right me-1"></i>Mes affectations
                        </a>
                    </li>
                    @endif

                    {{-- 4. Maintenance — Admin + Tech --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnicien())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('maintenances.*') ? 'active' : '' }}"
                        href="{{ route('maintenances.index') }}">
                            <i class="bi bi-wrench me-1"></i>Maintenance
                        </a>
                    </li>
                    @endif

                    {{-- 5. Rapports — Admin + Manager --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('rapports.*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bar-chart me-1"></i>Rapports
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('rapports.inventory') }}">
                                    <i class="bi bi-clipboard-data me-2 text-muted"></i>
                                    Inventaire des actifs
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('rapports.licenses') }}">
                                    <i class="bi bi-key me-2 text-muted"></i>
                                    Conformité licences
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('rapports.maintenances') }}">
                                    <i class="bi bi-wrench me-2 text-muted"></i>
                                    Coûts maintenance
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    {{-- 6. Administration — Admin only --}}
                    @if(Auth::user()->isAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('configuration*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i>Administration
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index') }}">
                                    <i class="bi bi-people me-2 text-muted"></i>Utilisateurs
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('configuration') }}">
                                    <i class="bi bi-sliders me-2 text-muted"></i>Configuration
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                href="{{ route('roles.index') }}">
                                    <i class="bi bi-shield-check me-2 text-muted"></i>Rôles & Permissions
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                </ul>

                {{-- Right side — user info + logout --}}
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                        href="#" role="button"
                        data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->first_name }}
                            <span class="badge bg-secondary ms-1 small">
                                {{ Auth::user()->getRoleNames()->first() ?? '—' }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small
                                            text-muted">
                                    {{ Auth::user()->full_name }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST"
                                    action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="dropdown-item
                                                text-danger">
                                        <i class="bi bi-box-arrow-right
                                                me-2"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
    @endauth

    {{-- Flash messages --}}
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Main content — child views render here --}}
    <main>
        @yield('content')
    </main>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    {{-- Page specific scripts --}}
    @stack('scripts')

</body>
</html>