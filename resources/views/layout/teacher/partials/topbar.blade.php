{{-- Topbar partial for teacher --}}
<header class="top-0 z-30">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
        <button
            class="rounded-xl p-2 text-slate-600 transition hover:bg-white hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 lg:hidden"
            @click="mobileOpen = true" aria-label="Open menu" type="button">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
            </svg>
        </button>

        <div class="flex-1 min-w-0">
            <div class="relative max-w-xl" @click.outside="searchOpen=false">
                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" placeholder="Search" x-model="searchTerm" @focus="searchOpen=true; closeMenus()"
                    @input="searchOpen=true" @keydown.enter.prevent="goSearch()"
                    class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5 text-sm text-slate-900
                        outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200" />

                <div x-show="searchOpen" x-cloak x-transition.origin.top.left
                    class="fixed left-3 right-3 top-20 z-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl
                    sm:absolute sm:left-0 sm:right-auto sm:top-auto sm:mt-2 sm:w-[min(36rem,calc(100vw-2rem))]">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-400"
                            x-text="searchTerm.trim() ? 'Search in' : 'Quick places'"></div>
                        <button type="button"
                            class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                            x-show="searchTerm.trim()"
                            @click="searchTerm=''; $nextTick(() => $root.querySelector('input[placeholder=Search]')?.focus())">
                            Clear
                        </button>
                    </div>

                    <div class="nav-scrollbar max-h-96 overflow-y-auto p-2">
                        <template x-for="item in filteredSearchItems()" :key="item.label">
                            <button type="button"
                                class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none"
                                @click="goSearch(item)">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-indigo-600">
                                    <i :class="item.icon" aria-hidden="true"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-bold text-slate-900"
                                        x-text="item.label"></span>
                                    <span class="block truncate text-xs text-slate-500"
                                        x-text="item.searchable && searchTerm.trim() ? `Find '${searchTerm.trim()}' here` : item.description"></span>
                                </span>
                                <i class="fa-solid fa-arrow-right text-xs text-slate-300" aria-hidden="true"></i>
                            </button>
                        </template>

                        <div class="px-4 py-8 text-center" x-show="filteredSearchItems().length === 0">
                            <div class="text-sm font-bold text-slate-800">No matching page</div>
                            <div class="mt-1 text-xs text-slate-500">Try classes, schedule, assignments, or attendance.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="ml-auto flex items-center gap-2">
            @include('layout.teacher.partials.actions')
        </div>
    </div>
</header>
