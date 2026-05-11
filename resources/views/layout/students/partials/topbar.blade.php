{{-- Topbar partial extracted from navbar.blade.php --}}
<header class="sticky top-0 z-30 backdrop-blur">
    <div class="flex min-h-16 flex-wrap items-center gap-2 px-4 sm:gap-3 md:flex-nowrap">

        <button class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 lg:hidden"
            @click="mobileOpen = true" aria-label="Open sidebar" type="button">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 6h16"></path>
                <path d="M4 12h16"></path>
                <path d="M4 18h16"></path>
            </svg>
        </button>

        {{-- Search --}}
        <div class="min-w-0 flex-1">
            <div class="relative max-w-xl">
                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path
                            d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                    </svg>
                </span>
                <input type="text" placeholder="Search"
                    class="w-full rounded-full border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-indigo-200 focus:ring-4 focus:ring-indigo-100" />
            </div>
        </div>

        {{-- Actions --}}
        <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
            @include('layout.students.partials.actions')
        </div>
    </div>
</header>
