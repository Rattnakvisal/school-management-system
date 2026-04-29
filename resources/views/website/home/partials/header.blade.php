{{-- =========================
    Sticky Auto-Hide Navbar
========================= --}}
<header x-data="websiteHomeHeader()" @scroll.window="handleScroll()"
    class="fixed inset-x-0 top-0 z-50 px-4 pt-4 transition-transform duration-300 sm:px-6 lg:pt-5"
    :class="showNavbar ? 'translate-y-0' : '-translate-y-full'">
    {{-- Navbar Container --}}
    <div data-reveal data-first style="--d:.02s" class="mx-auto max-w-7xl">
        <div class="home-header-shell flex items-center justify-between gap-4 rounded-[2rem] px-5 py-4 transition-all duration-300"
            :class="scrolled
                ?
                'border border-white/80 bg-white/95 shadow-xl ring-1 ring-slate-200/80 backdrop-blur-2xl' :
                'border border-white/70 bg-white/85 shadow-[0_24px_70px_-38px_rgba(37,99,235,0.35)] ring-1 ring-slate-200/60 backdrop-blur-2xl'">
            {{-- Logo / Brand --}}
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 transition hover:opacity-95">
                <img src="{{ $brandLogo ?? asset('images/techbridge-logo-mark.svg') }}" alt="{{ $schoolName }} logo"
                    class="h-14 w-14 shrink-0 object-contain drop-shadow-[0_18px_30px_-20px_rgba(24,80,200,0.55)]" />

                <div class="min-w-0">
                    <p
                        class="[font-family:Outfit,_sans-serif] truncate text-xl font-semibold leading-none tracking-tight text-slate-950 sm:text-2xl">
                        {{ $schoolName }}
                    </p>
                    <p class="mt-1 truncate text-[11px] font-extrabold uppercase tracking-[0.26em] text-slate-500">
                        {{ $brandTagline ?? \App\Support\HomePageContent::text('brand.tagline') }}
                    </p>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden items-center gap-7 lg:flex">
                @foreach ($links as $link)
                    <a href="{{ $link['href'] }}" @click="active = '{{ $link['href'] }}'"
                        class="home-nav-link relative text-sm font-semibold tracking-[-0.01em] transition duration-200"
                        :class="active === '{{ $link['href'] }}'
                            ?
                            'text-blue-700' :
                            'text-slate-600 hover:text-slate-950'">
                        {{ $link['label'] }}

                        <span
                            class="absolute inset-x-0 -bottom-2 mx-auto h-0.5 w-7 rounded-full bg-blue-600 transition-all duration-300"
                            :class="active === '{{ $link['href'] }}'
                                ?
                                'scale-100 opacity-100' :
                                'scale-0 opacity-0'"></span>
                    </a>
                @endforeach
            </nav>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route($dashboardRoute) }}"
                        class="home-header-cta hidden items-center justify-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_34px_-24px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_38px_-24px_rgba(37,99,235,0.85)] lg:inline-flex">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h7V3H3v9Zm11 9h7v-7h-7v7Zm0-18v7h7V3h-7ZM3 21h7v-5H3v5Z" />
                        </svg>
                        {{ \App\Support\HomePageContent::text('actions.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="home-header-cta hidden items-center justify-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_34px_-24px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_38px_-24px_rgba(37,99,235,0.85)] lg:inline-flex">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <path d="M10 17l5-5-5-5" />
                            <path d="M15 12H3" />
                        </svg>
                        {{ \App\Support\HomePageContent::text('actions.login') }}
                    </a>
                @endauth

                {{-- Mobile Menu Button --}}
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    aria-label="{{ \App\Support\HomePageContent::text('actions.toggle_menu') }}"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700 lg:hidden">
                    <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Dropdown Menu --}}
    <div x-show="open" x-cloak x-transition.opacity.duration.200ms @click.outside="open = false"
        class="px-4 pb-3 pt-2 sm:px-6 lg:hidden">
        <div class="mx-auto max-w-7xl">
            <div
                class="rounded-[1.75rem] border border-slate-200 bg-white/95 p-4 shadow-[0_20px_50px_-35px_rgba(15,23,42,0.32)] ring-1 ring-slate-200/60 backdrop-blur-xl">
                {{-- Mobile Links --}}
                <div class="grid gap-2 text-sm font-semibold text-slate-700">
                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}" @click="open = false; active = '{{ $link['href'] }}'"
                            class="rounded-2xl px-4 py-3 transition"
                            :class="active === '{{ $link['href'] }}'
                                ?
                                'bg-blue-50 text-blue-700' :
                                'hover:bg-slate-100'">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Mobile CTA --}}
                <div class="mt-4 grid gap-2">
                    @auth
                        <a href="{{ route($dashboardRoute) }}"
                            class="home-header-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 text-center text-sm font-bold text-white shadow-lg">
                            {{ \App\Support\HomePageContent::text('actions.dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="home-header-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 text-center text-sm font-bold text-white shadow-lg">
                            {{ \App\Support\HomePageContent::text('actions.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Navbar spacing because header is fixed --}}
<div class="h-[96px] lg:h-[108px]"></div>
