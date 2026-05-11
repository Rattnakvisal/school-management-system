{{-- Topbar partial extracted from navbar.blade.php --}}
@php
    if (!isset($sections)) {
        include resource_path('views/layout/admin/partials/nav-data.php');
    }
@endphp
<header class="top-0 z-30 transition-colors dark:border-slate-800 dark:bg-slate-950/80">
    <div class="min-h-16 px-4 sm:px-4 lg:px-4 flex flex-wrap items-center gap-2 sm:gap-3 md:flex-nowrap">

        {{-- Mobile menu button --}}
        <button
            class="lg:hidden p-2 rounded-xl hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
            @click="mobileOpen=true" aria-label="Open menu" type="button">
            <svg class="h-6 w-6 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
            </svg>
        </button>

        {{-- Search --}}
        <div class="flex-1 min-w-0">
            <div class="relative max-w-xl" @click.outside="searchOpen=false"
                @click="$el.querySelector('input')?.focus()">
                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 dark:text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                    </svg>
                </span>
                <input type="text" placeholder="Search" x-model="searchTerm" @focus="searchOpen=true; closeMenus()"
                    @input="searchOpen=true" @keydown.enter.prevent="goSearch()"
                    class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5 text-sm text-slate-900
                        outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500/20" />

                <div x-show="searchOpen" x-cloak x-transition.origin.top.left
                    class="fixed left-3 right-3 top-20 z-[85] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                    sm:absolute sm:left-0 sm:right-auto sm:top-auto sm:mt-2 sm:w-[min(36rem,calc(100vw-2rem))]">
                    <div
                        class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500"
                            x-text="searchTerm.trim() ? 'Search in' : 'Quick places'"></div>
                        <button type="button"
                            class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                            x-show="searchTerm.trim()"
                            @click="searchTerm=''; $nextTick(() => $root.querySelector('input[placeholder=Search]')?.focus())">
                            Clear
                        </button>
                    </div>

                    <div class="nav-scrollbar max-h-96 overflow-y-auto p-2">
                        <template x-for="item in filteredSearchItems()" :key="item.label">
                            <button type="button"
                                class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none dark:hover:bg-indigo-500/10 dark:focus:bg-indigo-500/10"
                                @click="goSearch(item)">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-indigo-600 dark:bg-slate-800 dark:text-indigo-300">
                                    <i :class="item.icon" aria-hidden="true"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-bold text-slate-900 dark:text-slate-100"
                                        x-text="item.label"></span>
                                    <span class="block truncate text-xs text-slate-500 dark:text-slate-400"
                                        x-text="item.searchable && searchTerm.trim() ? `Find '${searchTerm.trim()}' here` : item.description"></span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300 dark:text-slate-600"
                                    aria-hidden="true"></i>
                            </button>
                        </template>

                        <div class="px-4 py-8 text-center" x-show="filteredSearchItems().length === 0">
                            <div class="text-sm font-bold text-slate-800 dark:text-slate-100">No matching page</div>
                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try students, teachers,
                                classes, finance, or attendance.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
            @include('layout.admin.partials.actions')
        </div>
    </div>
</header>
