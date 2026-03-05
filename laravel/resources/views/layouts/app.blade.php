<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Atıksu Takip Sistemi')</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#d9041e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Atıksu Takip">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="icon" type="image/jpeg" sizes="192x192" href="{{ asset('images/logo.jpg') }}">
    
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
                    <button class="btn btn-outline-primary me-2" type="button" id="sidebarToggleBtn" style="z-index: 1; position: relative;">
                        <i class="bi bi-list" style="font-size: 1.25rem;"></i>
                    </button>
                @endif
            @endauth
            
            <a class="navbar-brand" href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}">
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
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Ana Menü -->
        <nav class="sidebar" id="sidebarMenu">
            <div class="sidebar-header">
                <h5><i class="bi bi-grid-3x3-gap"></i> Menü</h5>
                <button class="sidebar-close" onclick="closeSidebar()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sidebar-content">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people nav-icon"></i>
                    <span class="nav-text">Kullanıcılar</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.binalar.*') ? 'active' : '' }}" href="{{ route('admin.binalar.index') }}">
                    <i class="bi bi-building nav-icon"></i>
                    <span class="nav-text">Binalar</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.kontrol-maddeleri.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-maddeleri.index') }}">
                    <i class="bi bi-check2-square nav-icon"></i>
                    <span class="nav-text">Kontrol Maddeleri</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.kontrol-kayitlari.*') ? 'active' : '' }}" href="{{ route('admin.kontrol-kayitlari.index') }}">
                    <i class="bi bi-clipboard-check nav-icon"></i>
                    <span class="nav-text">Kontrol Kayıtları</span>
                </a>
                
                <!-- LABORATUVAR - NESTED MENU -->
                <a class="nav-link has-submenu {{ request()->routeIs('admin.laboratuvar.*') ? 'active' : '' }}" onclick="toggleSubmenu(this, event)">
                    <i class="bi bi-droplet-half nav-icon"></i>
                    <span class="nav-text">Laboratuvar</span>
                </a>
                <div class="submenu">
                    <a class="nav-link {{ request()->routeIs('admin.laboratuvar.index') ||  request()->routeIs('admin.laboratuvar.create') || request()->routeIs('admin.laboratuvar.show') || request()->routeIs('admin.laboratuvar.edit') ? 'active' : '' }}" href="{{ route('admin.laboratuvar.index') }}">
                        <i class="bi bi-list-ul nav-icon"></i>
                        <span class="nav-text">Raporlar</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.laboratuvar.grafikler') ? 'active' : '' }}" href="{{ route('admin.laboratuvar.grafikler') }}">
                        <i class="bi bi-graph-up nav-icon"></i>
                        <span class="nav-text">Grafikler</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.laboratuvar.excel-import') ? 'active' : '' }}" href="{{ route('admin.laboratuvar.excel-import') }}">
                        <i class="bi bi-file-earmark-excel nav-icon"></i>
                        <span class="nav-text">Excel İçe Aktar</span>
                    </a>
                </div>
                
                <a class="nav-link {{ request()->routeIs('admin.kontroller.gecmis-tarih*') ? 'active' : '' }}" href="{{ route('admin.kontroller.gecmis-tarih') }}">
                    <i class="bi bi-calendar-week nav-icon"></i>
                    <span class="nav-text">Geçmiş Tarihli Kontrol</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.personel-devam.*') ? 'active' : '' }}" href="{{ route('admin.personel-devam.index') }}">
                    <i class="bi bi-person-check nav-icon"></i>
                    <span class="nav-text">Personel Devam</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.is-takvimi.*') ? 'active' : '' }}" href="{{ route('admin.is-takvimi.index') }}">
                    <i class="bi bi-calendar-check nav-icon"></i>
                    <span class="nav-text">Aylık İş Takvimi</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.sayisal-analiz') ? 'active' : '' }}" href="{{ route('admin.sayisal-analiz') }}">
                    <i class="bi bi-graph-up-arrow nav-icon"></i>
                    <span class="nav-text">Sayısal Analiz</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.raporlar.*') ? 'active' : '' }}" href="{{ route('admin.raporlar.index') }}">
                    <i class="bi bi-file-earmark-text nav-icon"></i>
                    <span class="nav-text">Raporlar</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.arsivlenmis-isler.*') ? 'active' : '' }}" href="{{ route('admin.arsivlenmis-isler.index') }}">
                    <i class="bi bi-archive nav-icon"></i>
                    <span class="nav-text">Arşivlenmiş İşler</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.mail-ayarlari.*') ? 'active' : '' }}" href="{{ route('admin.mail-ayarlari.index') }}">
                    <i class="bi bi-envelope-at nav-icon"></i>
                    <span class="nav-text">Mail Ayarları</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                    <i class="bi bi-clock-history nav-icon"></i>
                    <span class="nav-text">Aktivite Logları</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.system-test.*') ? 'active' : '' }}" href="{{ route('admin.system-test.index') }}">
                    <i class="bi bi-heart-pulse nav-icon"></i>
                    <span class="nav-text">Sistem Teşhisi</span>
                </a>
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
        // Global fonksiyonlar - her zaman erişilebilir
        const isMobile = () => window.innerWidth <= 991;
        
        // Ana menüyü aç/kapat
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            
            if (!sidebar || !overlay) {
                console.error('Sidebar veya overlay bulunamadı!');
                return;
            }
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            if (sidebar.classList.contains('show')) {
                body.classList.add('sidebar-open');
            } else {
                body.classList.remove('sidebar-open');
            }
        }
        
        // Ana menüyü kapat
        function closeSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;
            
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            body.classList.remove('sidebar-open');
        }
        
        // Submenu toggle
        function toggleSubmenu(element, event) {
            event.preventDefault();
            event.stopPropagation();
            
            const submenu = element.nextElementSibling;
            const isExpanded = element.classList.contains('expanded');
            
            // Tüm diğer submenu'leri kapat
            document.querySelectorAll('.has-submenu.expanded').forEach(item => {
                if (item !== element) {
                    item.classList.remove('expanded');
                    item.nextElementSibling.classList.remove('expanded');
                }
            });
            
            // Bu submenu'yu toggle et
            if (isExpanded) {
                element.classList.remove('expanded');
                submenu.classList.remove('expanded');
            } else {
                element.classList.add('expanded');
                submenu.classList.add('expanded');
            }
        }
        
        // DOM yüklendiğinde event listener ekle
        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger buton event listener
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
            
            // Overlay click handler
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }
            
            // Aktif submenu'yu aç
            const activeSubmenuLink = document.querySelector('.submenu .nav-link.active');
            if (activeSubmenuLink) {
                const parentLink = activeSubmenuLink.closest('.submenu').previousElementSibling;
                if (parentLink && parentLink.classList.contains('has-submenu')) {
                    parentLink.classList.add('expanded');
                    parentLink.nextElementSibling.classList.add('expanded');
                }
            }
        });
    </script>
    @endif
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker kayıt başarılı:', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker kayıt hatası:', err);
                    });
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
