<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aladdin Karpet 11 - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Tambahkan di bagian head layout -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        :root {
            --sidebar-expanded-width: 280px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
            
        }
        
        /* Override green backgrounds with green */
        .bg-green-50 { background-color: #f0fdf4 !important; }
        .bg-green-100 { background-color: #dcfce7 !important; }
        .bg-green-200 { background-color: #bbf7d0 !important; }
        .bg-green-300 { background-color: #86efac !important; }
        .bg-green-400 { background-color: #4ade80 !important; }
        .bg-green-500 { background-color: #22c55e !important; }
        .bg-green-600 { background-color: #16a34a !important; }
        .bg-green-700 { background-color: #15803d !important; }
        .bg-green-800 { background-color: #166534 !important; }
        .bg-green-900 { background-color: #14532d !important; }
        
        /* Override green text with green */
        .text-green-50 { color: #f0fdf4 !important; }
        .text-green-100 { color: #dcfce7 !important; }
        .text-green-200 { color: #bbf7d0 !important; }
        .text-green-300 { color: #86efac !important; }
        .text-green-400 { color: #4ade80 !important; }
        .text-green-500 { color: #22c55e !important; }
        .text-green-600 { color: #16a34a !important; }
        .text-green-700 { color: #15803d !important; }
        .text-green-800 { color: #166534 !important; }
        .text-green-900 { color: #14532d !important; }
        
        /* Override green borders with green */
        .border-green-50 { border-color: #f0fdf4 !important; }
        .border-green-100 { border-color: #dcfce7 !important; }
        .border-green-200 { border-color: #bbf7d0 !important; }
        .border-green-300 { border-color: #86efac !important; }
        .border-green-400 { border-color: #4ade80 !important; }
        .border-green-500 { border-color: #22c55e !important; }
        .border-green-600 { border-color: #16a34a !important; }
        .border-green-700 { border-color: #15803d !important; }
        .border-green-800 { border-color: #166534 !important; }
        .border-green-900 { border-color: #14532d !important; }
        
        /* Override green rings with green */
        .ring-green-50 { border-color: #f0fdf4 !important; }
        .ring-green-100 { border-color: #dcfce7 !important; }
        .ring-green-200 { border-color: #bbf7d0 !important; }
        .ring-green-300 { border-color: #86efac !important; }
        .ring-green-400 { border-color: #4ade80 !important; }
        .ring-green-500 { border-color: #22c55e !important; }
        .ring-green-600 { border-color: #16a34a !important; }
        .ring-green-700 { border-color: #15803d !important; }
        .ring-green-800 { border-color: #166534 !important; }
        .ring-green-900 { border-color: #14532d !important; }

        body {
            overflow-x: hidden;
        }

        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-expanded-width);
            transition: all var(--transition-speed) ease;
            position: fixed;
            height: 100vh;
            z-index: 50;
            left: 0;
            top: 0;
            border-right: 1px solid #e5e7eb;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Main content area */
        .main-content {
            transition: all var(--transition-speed) ease;
            margin-left: var(--sidebar-expanded-width);
            width: calc(100% - var(--sidebar-expanded-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* Navbar styles */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 40;
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Content area */
        .content-area {
            flex: 1;
            overflow-y: auto;
            background-color: #f9fafb;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 45;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Active menu styling */
        .active-menu {
            background-color: #F0FDF4;
            border-left: 4px solid #15803D;
            color: #15803D;
        }

        /* Card shadow */
        .card-shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        /* Responsive breakpoints */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            .sidebar {
                transform: translateX(-100%);
                z-index: 60;
            }
            
            .sidebar.mobile-show {
                transform: translateX(0);
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            }
        }

        /* Sidebar specific styles */
        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.2s ease;
        }

        /* When sidebar is collapsed, center the icons */
        .sidebar.collapsed .px-4 {
            display: flex;
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* Hide dropdown arrows when sidebar is collapsed */
        .sidebar.collapsed [id$="DropdownArrow"] {
            display: none;
        }

        /* Only show icons in submenu when collapsed */
        .sidebar.collapsed .pl-8 {
            padding-left: 0;
        }

        /* Center the icons in collapsed state */
        .sidebar.collapsed .flex.items-center {
            justify-content: center;
        }

        /* Darker green color scheme */
        .text-green-700 {
            color: #15803D;
        }
        
        .bg-green-700 {
            background-color: #15803D;
        }
        
        .border-green-700 {
            border-color: #15803D;
        }
        
        .focus\:ring-green-700:focus {
            --tw-ring-color: #15803D;
        }
        
        .focus\:border-green-700:focus {
            border-color: #15803D;
        }
        
        .hover\:bg-green-700:hover {
            background-color: #15803D;
        }

        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    
    <!-- Layout Container -->
    <div class="flex h-screen overflow-hidden relative">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div id="mainContent" class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <div class="navbar" id="main-header">
                @include('layouts.navbar')
            </div>

            <!-- Content with proper spacing -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            lucide.createIcons();
            
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
            const toggleSidebar = document.getElementById('toggleSidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const toggleIcon = document.getElementById('toggleIcon');
            const bottomToggleIcon = document.getElementById('bottomToggleIcon');
            
            // Function to toggle sidebar state
            function toggleSidebarState() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('collapsed');
                
                // Rotate toggle icons
                if (toggleIcon) toggleIcon.classList.toggle('rotate-180');
                if (bottomToggleIcon) bottomToggleIcon.classList.toggle('rotate-180');
                
                // Store state in localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
            
            // Initialize sidebar state from localStorage
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                if (toggleIcon) toggleIcon.classList.add('rotate-180');
                if (bottomToggleIcon) bottomToggleIcon.classList.add('rotate-180');
            }
            
            // Desktop toggle buttons
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', toggleSidebarState);
            }
            
            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', toggleSidebarState);
            }
            
            // Mobile menu button
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-show');
                    sidebarOverlay.classList.toggle('active');
                    document.body.classList.toggle('overflow-hidden');
                });
            }
            
            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-show');
                    sidebarOverlay.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                });
            }
            
            // Close sidebar when clicking on a link (mobile)
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-show');
                        sidebarOverlay.classList.remove('active');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });
            
            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-show');
                    sidebarOverlay.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                    
                    // Apply collapsed state if it was saved
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('collapsed');
                    }
                } else {
                    // On mobile, always start with collapsed sidebar hidden
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                }
            }
            
            // Initial check
            handleResize();
            
            // Add resize listener with debounce
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(handleResize, 250);
            });
        });
    </script>
</body>
</html>