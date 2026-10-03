<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Kantin Ibnunnafis Depok</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Iconify Web Component -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <!-- Ibnunnafis POS Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">

    @stack('styles')
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Backdrop for Mobile -->
        <div id="sidebarBackdrop" class="sidebar-backdrop hidden" onclick="toggleSidebar(false)"></div>

        <!-- SIDEBAR -->
        <aside id="appSidebar" class="app-sidebar">
            <div class="sidebar-header">
                <div class="flex items-center gap-2">
                    <div class="sidebar-logo">
                        <img src="{{ asset('js/no wat.png') }}" alt="Logo Kantin Ibnunnafis">
                    </div>
                    <div class="sidebar-title">
                        <h2>Kantin Ibnunnafis</h2>
                        <span>Sistem Informasi Penjualan & Persediaan</span>
                    </div>
                </div>
                <button type="button" class="btn-icon sidebar-close-btn d-lg-none" onclick="toggleSidebar(false)">
                    <iconify-icon icon="lucide:x"></iconify-icon>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-heading">Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:layout-dashboard"></iconify-icon>
                    Dashboard
                </a>

                @if(auth()->user()->isKasir())
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:shopping-cart"></iconify-icon>
                    Kasir
                </a>
                @endif

                <div class="nav-heading">Persediaan & Master Data</div>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:package"></iconify-icon>
                    {{ auth()->user()->isKasir() ? 'Produk' : 'Monitoring Produk' }}
                </a>

                @if(auth()->user()->isKasir())
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:tags"></iconify-icon>
                    Kategori
                </a>
                <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:scale"></iconify-icon>
                    Satuan & Konversi
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:truck"></iconify-icon>
                    Mitra Supplier
                </a>
                <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:clipboard-check"></iconify-icon>
                    Barang Masuk
                </a>
                <a href="{{ route('stock-opname.index') }}" class="nav-link {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:boxes"></iconify-icon>
                    Stock Opname
                </a>
                @endif

                <div class="nav-heading">Histori & Laporan</div>
                <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <iconify-icon icon="hugeicons:transaction-history"></iconify-icon>
                    Riwayat Transaksi
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:bar-chart-3"></iconify-icon>
                    Laporan Penjualan
                </a>

                @if(auth()->user()->isAdmin())
                <div class="nav-heading">Pengaturan Sistem</div>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <iconify-icon icon="lucide:users-round"></iconify-icon>
                    Kelola Pengguna
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center min-w-0">
                        <div class="avatar-circle {{ auth()->user()->isAdmin() ? 'avatar-admin' : 'avatar-kasir' }}">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="user-meta">
                            <span class="user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                            <span class="badge {{ auth()->user()->isKasir() ? 'badge-primary' : 'badge-warning' }}">
                                {{ auth()->user()->role?->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="app-main">
            <!-- TOP NAVBAR -->
            <header class="app-header">
                <div class="header-left flex items-center gap-3">
                    <button type="button" class="btn-icon d-lg-none" onclick="toggleSidebar(true)">
                        <iconify-icon icon="lucide:menu"></iconify-icon>
                    </button>
                    <img class="header-brand-image" src="{{ asset('ibnunnafis-logo-removebg.png') }}" alt="Kantin Ibnunnafis">
                </div>

                <div class="header-right flex items-center gap-3">
                    <div class="system-time text-end d-none d-md-block">
                        <span class="fw-semibold text-slate-800 d-block" id="currentClock">--:--:--</span>
                        <span class="text-xs text-muted">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light text-dark border flex items-center gap-1.5 py-1 px-2.5 text-xs shadow-2xs" type="button" data-bs-toggle="dropdown" style="min-height: 34px; font-size: 0.8rem;">
                            <iconify-icon icon="lucide:user" class="fs-6"></iconify-icon>
                            <span class="d-none d-sm-inline fw-semibold">{{ auth()->user()->name }}</span>
                            <span class="badge {{ auth()->user()->isKasir() ? 'badge-primary' : 'badge-warning' }} ms-1" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">
                                {{ auth()->user()->role?->name }}
                            </span>
                            <iconify-icon icon="lucide:chevron-down" class="fs-6 text-muted"></iconify-icon>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.825rem; min-width: 200px;">
                            <li class="px-3 py-2 border-bottom">
                                <span class="d-block fw-bold text-xs text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="text-2xs text-muted font-mono">ID: {{ auth()->user()->username }}</span>
                            </li>
                            <li class="p-1">
                                <button type="button" class="dropdown-item text-danger rounded-2 flex items-center gap-2 py-1.5 px-2.5 text-xs fw-semibold" onclick="confirmLogout()">
                                    <iconify-icon icon="lucide:log-out" class="fs-6"></iconify-icon>
                                    Keluar (Logout)
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            <main class="app-content p-3 p-md-4">
                @hasSection('page-header')
                <div class="mb-4">
                    <h1 class="h4 fw-bold text-slate-800 mb-1">@yield('page-header')</h1>
                    @hasSection('page-subtitle')
                    <p class="text-sm text-muted mb-0">@yield('page-subtitle')</p>
                    @endif
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: @json(session('success')),
            showConfirmButton: false,
            timer: 2800,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'pos-top-toast'
            },
            showClass: {
                popup: 'swal2-show-top-toast'
            },
            hideClass: {
                popup: 'swal2-hide-top-toast'
            }
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'error',
            title: @json(session('error')),
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'pos-top-toast'
            },
            showClass: {
                popup: 'swal2-show-top-toast'
            },
            hideClass: {
                popup: 'swal2-hide-top-toast'
            }
        });
    </script>
    @endif
    @if(session('info'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'info',
            title: @json(session('info')),
            showConfirmButton: false,
            timer: 2800,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            customClass: {
                popup: 'pos-top-toast'
            },
            showClass: {
                popup: 'swal2-show-top-toast'
            },
            hideClass: {
                popup: 'swal2-hide-top-toast'
            }
        });
    </script>
    @endif
    @if($errors->any())
    <script>Swal.fire({ icon: 'error', title: 'Terdapat kesalahan input', html: @json(implode('<br>', $errors->all())), confirmButtonColor: '#059669' });</script>
    @endif

    <script>
                document.addEventListener('DOMContentLoaded', function() {
            // 1. Auto-relocate modals to <body> to prevent stacking context or backdrop trap
            document.querySelectorAll('.modal').forEach(function(modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });

            // 2. Preserve & restore sidebar scroll position and keep active menu in view
            const sidebarNav = document.querySelector('.sidebar-nav');
            if (sidebarNav) {
                const savedScroll = sessionStorage.getItem('sidebarNavScrollTop');
                if (savedScroll !== null) {
                    sidebarNav.scrollTop = parseInt(savedScroll, 10);
                }

                // Ensure active menu item is scrolled into view (e.g. Riwayat Transaksi / Laporan)
                const activeLink = sidebarNav.querySelector('.nav-link.active');
                if (activeLink) {
                    activeLink.scrollIntoView({ block: 'nearest' });
                }

                // Save scroll position when user scrolls sidebar
                sidebarNav.addEventListener('scroll', function() {
                    sessionStorage.setItem('sidebarNavScrollTop', sidebarNav.scrollTop);
                }, { passive: true });

                // Also save on clicking any navigation link
                sidebarNav.querySelectorAll('.nav-link').forEach(function(link) {
                    link.addEventListener('click', function() {
                        sessionStorage.setItem('sidebarNavScrollTop', sidebarNav.scrollTop);
                    });
                });

                window.addEventListener('beforeunload', function() {
                    sessionStorage.setItem('sidebarNavScrollTop', sidebarNav.scrollTop);
                });
            }
        });

        function toggleSidebar(open) {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (open) {
                sidebar.classList.add('mobile-open');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.remove('mobile-open');
                backdrop.classList.add('hidden');
            }
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Keluar dari aplikasi?',
                text: 'Apakah Anda yakin ingin mengakhiri sesi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Live clock
        function updateClock() {
            const clockEl = document.getElementById('currentClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>