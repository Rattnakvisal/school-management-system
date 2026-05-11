@php
    if (!isset($sections)) {
        include resource_path('views/layout/teacher/partials/nav-data.php');
    }
@endphp
<aside
    class="fixed inset-y-0 left-0 z-50 flex h-screen flex-col overflow-hidden border-r border-slate-200 bg-white shadow-sm transition-all duration-300"
    :class="[
        isDesktop ?
        (sidebarCollapsed ? 'translate-x-0 w-16' : 'translate-x-0 w-56') :
        (mobileOpen ? 'translate-x-0 w-[84vw] max-w-[19rem]' : '-translate-x-full w-[84vw] max-w-[19rem]')
    ]"
    aria-label="Sidebar">

    {{-- Header --}}
    <div class="flex h-14 items-center border-b border-slate-200 shrink-0"
        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
        <a href="{{ route('teacher.dashboard') }}" class="flex min-w-0 items-center gap-2" x-show="!sidebarCollapsed"
            x-transition>
            <img src="{{ $schoolBrandLogo ?? asset('images/techbridge-logo-mark.svg') }}"
                alt="{{ $schoolBrandName ?? 'TechBridge Academy' }} logo" class="h-9 w-9 shrink-0 object-contain" />

            <div class="min-w-0">
                <div class="truncate text-sm font-extrabold tracking-tight text-slate-900">
                    {{ $schoolBrandName ?? 'TechBridge Academy' }}</div>
                <div class="truncate text-xs text-slate-500">
                    {{ $schoolBrandTagline ?? 'Teacher Panel' }}</div>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <button type="button"
                class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:inline-flex"
                @click="toggleSidebar()" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg x-show="!sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"
                    aria-hidden="true">
                    <path d="M4 6h16v2H4V6Zm0 5h10v2H4v-2Zm0 5h16v2H4v-2Z" />
                </svg>
                <svg x-show="sidebarCollapsed" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"
                    aria-hidden="true">
                    <path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
                </svg>
            </button>

            <button
                class="rounded-2xl p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
                @click="mobileOpen = false" aria-label="Close sidebar" type="button">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="nav-scrollbar flex-1 space-y-3 overflow-y-auto py-3" :class="sidebarCollapsed ? 'px-2' : 'px-3'">

        @foreach ($sections as $section)
            @php
                $sectionKey = $section['key'];
                $sectionIsActive = collect($section['items'])->contains(fn($navItem) => $navItem['active']);
            @endphp
            <div class="space-y-1"
                @if ($sectionIsActive) x-init="openSidebarSection('{{ $sectionKey }}')" @endif>
                <button type="button" x-show="!sidebarCollapsed" x-transition
                    @click="toggleSidebarSection('{{ $sectionKey }}')"
                    :aria-expanded="isSidebarSectionOpen('{{ $sectionKey }}')"
                    class="group flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 transition hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <span class="min-w-0 flex-1 truncate">{{ $section['title'] }}</span>
                    <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200 group-hover:text-slate-600"
                        :class="isSidebarSectionOpen('{{ $sectionKey }}') ? 'rotate-0' : '-rotate-90'"
                        aria-hidden="true"></i>
                </button>

                <div class="space-y-1" x-show="sidebarCollapsed || isSidebarSectionOpen('{{ $sectionKey }}')"
                    x-transition>
                    @foreach ($section['items'] as $l)
                        <a href="{{ route($l['route']) }}"
                            class="group relative flex min-h-10 items-center rounded-xl text-sm transition duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-100 {{ $l['active'] ? 'bg-indigo-600 text-white shadow-[0_12px_24px_-18px_rgba(79,70,229,0.8)]' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950' }}"
                            :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'gap-2 px-3 py-2.5'">

                            @if ($l['active'])
                                <span class="absolute left-0 top-2 bottom-2 w-0.5 rounded-r-full bg-white"></span>
                            @endif

                            <span
                                class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm transition {{ $l['active'] ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white group-hover:text-indigo-600' }}">
                                <i class="{{ $teacherNavIconClasses[$l['icon']] ?? 'fa-solid fa-circle' }}"
                                    aria-hidden="true"></i>
                            </span>

                            <div x-show="!sidebarCollapsed" x-transition class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold">{{ $l['label'] }}</div>
                            </div>

                            <div x-show="!sidebarCollapsed">
                                @if ($l['active'])
                                    <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                @endif
                            </div>

                            <div x-show="sidebarCollapsed"
                                class="pointer-events-none absolute left-full ml-2 hidden whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-lg group-hover:block lg:group-hover:block">
                                {{ $l['label'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
</aside>
