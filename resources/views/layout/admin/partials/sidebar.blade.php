@php
    if (!isset($sections)) {
        include resource_path('views/layout/admin/partials/nav-data.php');
    }
@endphp
<aside
    class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col border-r border-slate-200 bg-white shadow-sm transition-all duration-300 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/30 lg:translate-x-0"
    :class="[
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        collapsed ? 'w-16' : 'w-56'
    ]"
    aria-label="Sidebar">
    {{-- Header --}}
    <div class="flex h-14 items-center border-b border-slate-200 shrink-0 dark:border-slate-800"
        :class="collapsed ? 'justify-center px-2' : 'justify-between px-3'">
        <a href="{{ route($adminShellDashboardRoute) }}" class="flex min-w-0 items-center gap-2" x-show="!collapsed"
            x-transition>
            <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo" class="h-9 w-9 shrink-0 object-contain" />

            <div class="min-w-0">
                <div class="truncate text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $schoolBrandName ?? 'TechBridge Academy' }}
                </div>
                <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                    {{ $schoolBrandTagline ?? $adminShellLabel . ' Dashboard' }}</div>
            </div>
        </a>

        <div class="flex items-center gap-2">
            {{-- desktop collapse --}}
            <button type="button"
                class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white dark:focus:ring-indigo-500/20 lg:inline-flex"
                @click="toggleSidebar()" :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg x-show="!collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                </svg>
                <svg x-show="collapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                </svg>
            </button>

            {{-- mobile close --}}
            <button
                class="inline-flex lg:hidden h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
                @click="mobileOpen=false" type="button" aria-label="Close sidebar">
                &times;
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="nav-scrollbar flex-1 overflow-y-auto py-3" :class="collapsed ? 'px-2' : 'px-3'">
        @php
        @endphp
        <nav class="space-y-3">
            @foreach ($sections as $section)
                @php
                    if (empty($section['items'])) {
                        continue;
                    }

                    $sectionKey = $section['key'];
                    $sectionIsActive = collect($section['items'])->contains(fn($navItem) => $navItem['active']);
                    $sectionBadgeCount = collect($section['items'])->sum(fn($navItem) => $navItem['badge'] ?? 0);
                @endphp
                <div class="space-y-1"
                    @if ($sectionIsActive) x-init="openSidebarSection('{{ $sectionKey }}')" @endif>
                    <button type="button" x-show="!collapsed" x-transition
                        @click="toggleSidebarSection('{{ $sectionKey }}')"
                        :aria-expanded="isSidebarSectionOpen('{{ $sectionKey }}')"
                        class="group flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 transition hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:text-slate-500 dark:hover:bg-slate-800/80 dark:hover:text-slate-200 dark:focus:ring-indigo-500/20">
                        <span class="min-w-0 flex-1 truncate">{{ $section['title'] }}</span>
                        @if ($sectionBadgeCount > 0)
                            <span
                                class="grid h-4 min-w-4 place-items-center rounded-full bg-red-100 px-1 text-[9px] font-black tracking-normal text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                {{ $sectionBadgeCount > 99 ? '99+' : $sectionBadgeCount }}
                            </span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200 group-hover:text-slate-600 dark:group-hover:text-slate-300"
                            :class="isSidebarSectionOpen('{{ $sectionKey }}') ? 'rotate-0' : '-rotate-90'"
                            aria-hidden="true"></i>
                    </button>

                    <div class="space-y-1" x-show="collapsed || isSidebarSectionOpen('{{ $sectionKey }}')"
                        x-transition>
                        @foreach ($section['items'] as $l)
                            <a href="{{ route($l['route']) }}"
                                class="group relative flex min-h-10 items-center rounded-xl text-sm transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-[0_12px_24px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800/80 dark:hover:text-white' }}"
                                :class="collapsed ? 'justify-center px-2 py-2.5' : 'gap-2 px-3 py-2.5'">

                                @if ($l['active'])
                                    <span class="absolute left-0 top-2 bottom-2 w-0.5 rounded-r-full bg-white"></span>
                                @endif

                                <span
                                    class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white group-hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-slate-700 dark:group-hover:text-indigo-300' }}">
                                    <i class="{{ $adminNavIconClasses[$l['icon']] ?? 'fa-solid fa-circle' }}"
                                        aria-hidden="true"></i>

                                    @if (($l['badge'] ?? 0) > 0)
                                        <span
                                            class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 {{ $l['active'] ? 'ring-indigo-600' : 'ring-white dark:ring-slate-900' }}"></span>
                                    @endif
                                </span>

                                <div x-show="!collapsed" x-transition class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-semibold">{{ $l['label'] }}</div>
                                </div>

                                <div x-show="!collapsed">
                                    @if (($l['badge'] ?? 0) > 0)
                                        <span
                                            class="inline-flex min-w-[24px] items-center justify-center rounded-full px-2 py-1 text-[10px] font-bold {{ $l['active'] ? 'bg-white text-indigo-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $l['badge'] > 99 ? '99+' : $l['badge'] }}
                                        </span>
                                    @elseif ($l['active'])
                                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                    @endif
                                </div>

                                <div x-show="collapsed"
                                    class="pointer-events-none absolute left-full ml-2 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg ring-1 ring-white/10 group-hover:block dark:bg-slate-800 lg:group-hover:block">
                                    {{ $l['label'] }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </div>
</aside>
