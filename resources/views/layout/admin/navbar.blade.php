<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @php
        $adminShellUser = auth()->user();
        $adminShellRole = strtolower(trim((string) (auth()->user()?->role ?? 'admin')));
        $adminShellLabel = $adminShellRole === 'staff' ? 'Staff' : 'Admin';
        $adminShellDashboardRoute =
            $adminShellRole === 'staff'
                ? \App\Support\StaffPermissions::firstRouteName($adminShellUser)
                : 'admin.dashboard';
        $adminShellNotificationReadRoute =
            $adminShellRole === 'staff' ? 'staff.notifications.readAll' : 'admin.notifications.readAll';
    @endphp
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | ' . $adminShellLabel . ' Dashboard' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    <script>
        (() => {
            try {
                const savedTheme = localStorage.getItem('admin_theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const darkMode = savedTheme ? savedTheme === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', darkMode);
                document.documentElement.style.colorScheme = darkMode ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/admin-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100"
    data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading student panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>
    <div x-data="adminShell()" x-init="init()" @keydown.escape.window="closeAll()"
        class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800 transition-colors duration-200 dark:bg-slate-950 dark:text-slate-100"
        data-admin-shell data-loading-shell>
        @php
            if (!isset($sections)) {
                include resource_path('views/layout/admin/partials/nav-data.php');
        } @endphp
        @include('layout.admin.partials.sidebar') <div class="min-h-screen flex flex-col overflow-x-hidden"
            :class="collapsed ? 'lg:pl-16' : 'lg:pl-56'">
            @include('layout.admin.partials.topbar')
            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-x-hidden p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>

    <script>
        window.adminNavbarSearchItems = @json($adminSearchTargets ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
