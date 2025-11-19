<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Sae Bakery') - Sae Bakery</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @yield('styles')
</head>
<body>
    <div class="layout-container">
        <!-- Sidebar Toggle Button (Mobile) -->
        <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            @include('components.sidebar')
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                @include('components.header')
            </header>

            <!-- Content Wrapper -->
            <section class="content-wrapper">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="alert alert-success">
                        <span>✓</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <span>✕</span>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <span>✕</span>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        // Toggle submenu functionality
        function toggleSubmenu(event, menuId) {
            event.preventDefault();
            const submenu = document.getElementById(menuId);
            const toggle = event.currentTarget;
            
            // Close all other submenus
            const allSubmenus = document.querySelectorAll('.submenu.active');
            const allToggles = document.querySelectorAll('.menu-toggle.active');
            
            allSubmenus.forEach(sm => {
                if (sm.id !== menuId) {
                    sm.classList.remove('active');
                }
            });
            
            allToggles.forEach(mt => {
                if (mt !== toggle) {
                    mt.classList.remove('active');
                }
            });

            submenu.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        // Set active menu item based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.pathname;
            const menuItems = document.querySelectorAll('.submenu-item');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') === currentUrl) {
                    item.classList.add('active');
                    const submenu = item.closest('.submenu');
                    if (submenu) {
                        submenu.classList.add('active');
                        const toggle = submenu.previousElementSibling;
                        if (toggle) toggle.classList.add('active');
                    }
                }
            });
        });

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                }
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
