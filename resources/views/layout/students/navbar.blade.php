<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | Student Dashboard' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/student-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800" data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading student panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>

    <div x-data="studentShell()" x-init="init()" @keydown.escape.window="closeAll()"
        class="min-h-screen overflow-x-hidden" data-student-notification-root
        data-notification-poll-url="{{ route('student.notifications.poll') }}"
        data-notification-readall-url="{{ route('student.notifications.readAll') }}"
        data-notification-latest-id="{{ (int) ($navNotifs->first()->id ?? 0) }}"
        data-notification-unread-count="{{ (int) ($navUnread ?? 0) }}">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen = false" aria-hidden="true"></div>

        @php
            if (!isset($sections)) {
                include resource_path('views/layout/students/partials/nav-data.php');
            }
        @endphp

        @include('layout.students.partials.sidebar')

        {{-- MAIN --}}
        <div class="flex min-h-screen flex-col overflow-x-hidden transition-[padding] duration-300"
            :class="isDesktop ? (sidebarCollapsed ? 'pl-16' : 'pl-56') : 'pl-0'">

            @include('layout.students.partials.topbar')

            <main class="p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
