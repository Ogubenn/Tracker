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
            @auth
                @if(auth()->user()->isAdmin())
                    <button class="btn btn-outline-primary me-2" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list" style="font-size: 1.25rem;"></i>
                    </button>
                @endif
            @endauth
            
            <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('personel.dashboard') }}">
                <img src="{{ asset('images/logo.jpg') }}" alt="Bulancak Belediyesi" class="navbar-logo">
                <div class="navbar-text">
                    <span class="navbar-title">Bulancak Belediyesi</span>
                    <small class="navbar-subtitle">Atıksu Takip Sistemi</small>
                </div>
            </a>
            
            <div class="d-flex align-items-center ms-auto order-lg-2">
                @auth
                    
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <span class="d-none d-sm-inline">{{ auth()->user()->ad }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->ad }}</h6></li>
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                            <li><span class="dropdown-item-text small"><span class="badge bg-{{ auth()->user()->rol === 'admin' ? 'danger' : 'info' }}">{{ ucfirst(auth()->user()->rol) }}</span></span></li>
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
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        <nav class="sidebar" id="sidebarMenu">
            <div class="sidebar-content">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" data-title="Dashboard">
                            <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" data-title="Kullanıcılar">
                            <i class="bi bi-people"></i> <span class="nav-text">Kullanıcılar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.binalar.*') ? 'active' : '' }}" href="{{ route('admin.binalar.index') }}" data-title="Binalar">
                            <i class="bi bi-building"></i> <span class="nav-text">Binalar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kontrol-maddeleri.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-maddeleri.index') }}" data-title="Kontrol Maddeleri">
                            <i class="bi bi-check2-square"></i> <span class="nav-text">Kontrol Maddeleri</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kontrol-kayitlari.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-kayitlari.index') }}" data-title="Kontrol Kayıtları">
                            <i class="bi bi-clipboard-check"></i> <span class="nav-text">Kontrol Kayıtları</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sayisal-analiz') ? 'active' : '' }}" href="{{ route('admin.sayisal-analiz') }}" data-title="Sayısal Analiz">
                            <i class="bi bi-graph-up-arrow"></i> <span class="nav-text">Sayısal Analiz</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.raporlar.*') ? 'active' : '' }}" href="{{ route('admin.raporlar.index') }}" data-title="Raporlar">
                            <i class="bi bi-file-earmark-text"></i> <span class="nav-text">Raporlar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mail-ayarlari.*') ? 'active' : '' }}" href="{{ route('admin.mail-ayarlari.index') }}" data-title="Mail Ayarları">
                            <i class="bi bi-envelope-at"></i> <span class="nav-text">Mail Ayarları</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}" data-title="Aktivite Logları">
                            <i class="bi bi-clock-history"></i> <span class="nav-text">Aktivite Logları</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.system-test.*') ? 'active' : '' }}" href="{{ route('admin.system-test.index') }}" data-title="Sistem Teşhisi">
                            <i class="bi bi-heart-pulse"></i> <span class="nav-text">Sistem Teşhisi</span>
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
    
    @if(auth()->check() && auth()->user()->isAdmin())
    <script>
        const isMobile = () => window.innerWidth <= 991;
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            const mainContent = document.querySelector('.main-content');
            
            if (isMobile()) {
                // Mobile: Overlay mode
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                body.classList.toggle('sidebar-open');
            } else {
                // Desktop: Collapsed mode
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                
                // LocalStorage'a kaydet
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        }
        
        // Tooltip sistemi
        function initTooltips() {
            const navLinks = document.querySelectorAll('.sidebar.collapsed .nav-link');
            navLinks.forEach(link => {
                const tooltip = document.createElement('div');
                tooltip.className = 'sidebar-tooltip';
                tooltip.textContent = link.getAttribute('data-title');
                link.appendChild(tooltip);
            });
        }
        
        function removeTooltips() {
            document.querySelectorAll('.sidebar-tooltip').forEach(t => t.remove());
        }
        
        // Sidebar state observer
        let tooltipInitialized = false;
        const sidebar = document.getElementById('sidebarMenu');
        const observer = new MutationObserver(() => {
            if (sidebar.classList.contains('collapsed') && !isMobile()) {
                if (!tooltipInitialized) {
                    setTimeout(initTooltips, 50);
                    tooltipInitialized = true;
                }
            } else {
                removeTooltips();
                tooltipInitialized = false;
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.querySelector('.main-content');
            
            if (isMobile()) {
                // Mobile: Kapalı başla
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                // Desktop: LocalStorage'dan oku
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    setTimeout(initTooltips, 100);
                    tooltipInitialized = true;
                }
            }
            
            // Observer başlat
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        });
        
        // Window resize handling
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const mainContent = document.querySelector('.main-content');
                
                if (isMobile()) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    overlay.classList.remove('show');
                    sidebar.classList.remove('show');
                    removeTooltips();
                    tooltipInitialized = false;
                } else {
                    overlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed && !tooltipInitialized) {
                        setTimeout(initTooltips, 100);
                        tooltipInitialized = true;
                    }
                }
            }, 250);
        });
    </script>
    @endif
    
    @stack('scripts')
</body>
</html>
