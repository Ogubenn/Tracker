<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Atıksu Takip Sistemi')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('personel.dashboard') }}">
                <img src="{{ asset('images/logo.jpg') }}" alt="Bulancak Belediyesi" class="navbar-logo">
                <div class="navbar-text">
                    <span class="navbar-title">Bulancak Belediyesi</span>
                    <small class="navbar-subtitle">Atıksu Takip Sistemi</small>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->ad }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small">Rol: {{ auth()->user()->rol }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(auth()->check() && auth()->user()->isAdmin())
        <nav class="sidebar d-md-block" id="sidebarMenu">
            <div class="sidebar-content">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Kullanıcılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.binalar.*') ? 'active' : '' }}" href="{{ route('admin.binalar.index') }}">
                            <i class="bi bi-building"></i> Binalar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kontrol-maddeleri.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-maddeleri.index') }}">
                            <i class="bi bi-check2-square"></i> Kontrol Maddeleri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kontrol-kayitlari.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-kayitlari.index') }}">
                            <i class="bi bi-clipboard-check"></i> Kontrol Kayıtları
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.istatistikler.*') ? 'active' : '' }}" href="{{ route('admin.istatistikler.index') }}">
                            <i class="bi bi-bar-chart-line"></i> İstatistikler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.raporlar.*') ? 'active' : '' }}" href="{{ route('admin.raporlar.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Raporlar
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <main class="main-content">
            @yield('content')
        </main>
    @else
        <main class="main-content" style="margin-left: 0;">
            @yield('content')
        </main>
    @endif

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
