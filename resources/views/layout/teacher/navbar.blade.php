<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? ($schoolBrandName ?? 'TechBridge Academy') . ' | Teacher Dashboard' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/navbar/teacher-navbar.js'])
</head>

<body class="min-h-full overflow-x-hidden bg-slate-100 text-slate-800" data-loading-shell>
    <div id="admin-swirling-loader" class="admin-swirling-loader" role="status" aria-live="polite"
        aria-label="Loading teacher panel">
        <svg class="admin-swirling-loader__icon" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" focusable="false">
            <circle class="loading-ui-swirling-circle" cx="400" cy="400" r="200" fill="none"
                stroke="currentColor" stroke-linecap="round" stroke-width="50" />
        </svg>
    </div>

    <div x-data="teacherShell()" x-init="init()" @keydown.escape.window="closeAll()" class="min-h-screen"
        data-teacher-notification-root data-notification-poll-url="{{ route('teacher.notifications.poll') }}"
        data-notification-readall-url="{{ route('teacher.notifications.readAll') }}"
        data-notification-latest-id="{{ (int) ($navNotifs->first()->id ?? 0) }}"
        data-notification-unread-count="{{ (int) ($navUnread ?? 0) }}">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="mobileOpen = false" aria-hidden="true"></div>

        @php
            if (!isset($sections)) {
                include resource_path('views/layout/teacher/partials/nav-data.php');
            }
        @endphp

        @include('layout.teacher.partials.sidebar')

        {{-- MAIN AREA --}}
        <div class="min-h-screen flex flex-col transition-[padding] duration-300"
            :class="isDesktop ? (sidebarCollapsed ? 'pl-16' : 'pl-56') : 'pl-0'">

            @include('layout.teacher.partials.topbar')

            <main class="flex-1 p-4 sm:p-6" data-page-animate>
                @yield('page')
            </main>
        </div>
    </div>

    <script>
        window.teacherNavbarSearchItems = @json($teacherSearchTargets ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
