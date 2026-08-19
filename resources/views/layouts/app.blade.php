<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <span class="brand-mark">@include('partials.icon', ['name' => 'wallet', 'size' => 20])</span>
                    <span class="brand-text">SILKA<small>Keuangan</small></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'dashboard', 'size' => 18]) Dashboard
                </a>
                <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'transaksi', 'size' => 18]) Transaksi
                </a>
                <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'kategori', 'size' => 18]) Kategori
                </a>
                <a href="{{ route('coa.index') }}" class="{{ request()->routeIs('coa.*') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'coa', 'size' => 18]) COA
                </a>
                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'laporan', 'size' => 18]) Laporan
                </a>
                <a href="{{ route('target-capaians.index') }}" class="{{ request()->routeIs('target-capaians.*') ? 'active' : '' }}">
                    @include('partials.icon', ['name' => 'target', 'size' => 18]) Target Capaian
                </a>
                @can('manage-users')
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        @include('partials.icon', ['name' => 'users', 'size' => 18]) User
                    </a>
                @endcan
            </nav>

            <div class="sidebar-footer">
                @php
                    $initials = collect(explode(' ', auth()->user()->name))
                        ->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                @endphp
                <div class="side-footer-user">
                    <div class="avatar" style="width:34px;height:34px;font-size:12px">{{ $initials }}</div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <small>{{ auth()->user()->isAdmin() ? 'Administrator' : 'Bendahara' }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-block">
                        @include('partials.icon', ['name' => 'logout', 'size' => 15]) Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main">
            <header class="topbar">
                <button class="navbar-toggler" type="button" id="sidebarToggle" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="topbar-title">
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
                <div class="topbar-user">
                    <div class="avatar">{{ $initials }}</div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <button type="button" class="logout-btn" data-logout>
                        @include('partials.icon', ['name' => 'logout', 'size' => 15]) Logout
                    </button>
                </div>
            </header>

            <main class="content">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        @include('partials.icon', ['name' => 'check', 'size' => 18])
                        <span>{{ session('success') }}</span>
                        <button type="button" class="alert-close" aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 16])</button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        @include('partials.icon', ['name' => 'x', 'size' => 18])
                        <span>{{ session('error') }}</span>
                        <button type="button" class="alert-close" aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 16])</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @include('partials.icon', ['name' => 'info', 'size' => 18])
                        <div>
                            <strong>Periksa kembali input Anda:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="alert-close" aria-label="Tutup">@include('partials.icon', ['name' => 'x', 'size' => 16])</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var toggle = document.getElementById('sidebarToggle');

            function closeSidebar() {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('show');
            }

            if (toggle) toggle.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('show');
            });

            if (overlay) overlay.addEventListener('click', closeSidebar);

            // Logout (tombol di topbar) submit form logout pertama di sidebar-footer
            var logoutBtn = document.querySelector('[data-logout]');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function () {
                    var form = document.querySelector('.sidebar-footer form[action]');
                    if (form) form.submit();
                });
            }

            // Tutup alert
            document.querySelectorAll('.alert-close').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var alertEl = btn.closest('.alert');
                    if (alertEl) alertEl.remove();
                });
            });

            // Auto-dismiss alert sukses
            document.querySelectorAll('.alert-success').forEach(function (alertEl) {
                setTimeout(function () {
                    if (alertEl.parentNode) {
                        alertEl.style.transition = 'opacity .4s ease';
                        alertEl.style.opacity = '0';
                        setTimeout(function () { if (alertEl.parentNode) alertEl.remove(); }, 420);
                    }
                }, 4500);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>