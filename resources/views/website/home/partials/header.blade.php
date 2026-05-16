{{-- =========================
Header
========================= --}}
<header x-data="websiteHomeHeader()" @scroll.window="handleScroll()"
    class="fixed inset-x-0 top-0 z-50 transition-transform duration-300"
    :class="showNavbar ? 'translate-y-0' : '-translate-y-full'">
    <div class="bg-[#071714] text-white">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2 text-[11px] font-semibold sm:px-6">
            <p class="hidden min-w-0 truncate text-white/75 sm:block">
                {{ $brandTagline ?? \App\Support\HomePageContent::text('brand.tagline') }}
            </p>

            <div class="ml-auto flex items-center gap-4">
                <a href="#contact" class="hidden items-center gap-2 text-white/80 transition hover:text-white md:flex">
                    <i class="fa-regular fa-envelope text-[10px] text-emerald-300"></i>
                    {{ \App\Support\HomePageContent::text('actions.contact_admissions') }}
                </a>

                @auth
                    <a href="{{ route($dashboardRoute) }}" class="text-white/80 transition hover:text-white">
                        {{ \App\Support\HomePageContent::text('actions.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-white/80 transition hover:text-white">
                        {{ \App\Support\HomePageContent::text('actions.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div
        class="border-b border-emerald-950/5 bg-white/95 shadow-[0_18px_60px_-42px_rgba(15,23,42,0.45)] backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:py-4">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5 transition hover:opacity-90">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 ring-1 ring-emerald-100">
                    <img src="{{ $brandLogo ?? asset('images/techbridge-logo-mark.svg') }}" alt="{{ $schoolName }} logo"
                        class="h-8 w-8 object-contain" />
                </span>

                <span class="min-w-0">
                    <span
                        class="[font-family:Outfit,_sans-serif] block truncate text-lg font-bold tracking-tight text-slate-950">
                        {{ $schoolName }}
                    </span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 lg:flex">
                @foreach ($links as $link)
                    <a href="{{ $link['href'] }}" @click="active = '{{ $link['href'] }}'"
                        class="group inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-600 transition hover:text-emerald-600"
                        :class="active === '{{ $link['href'] }}' ? 'text-emerald-600' : ''">
                        {{ $link['label'] }}
                        @if (!$loop->first && !$loop->last)

                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="#contact"
                    class="hidden h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-emerald-200 hover:text-emerald-600 sm:inline-flex"
                    aria-label="{{ \App\Support\HomePageContent::text('actions.contact_admissions') }}">
                    <i class="fa-regular fa-message text-sm"></i>
                </a>

                @auth
                    <a href="{{ route($dashboardRoute) }}"
                        class="hidden items-center justify-center rounded-full bg-[#071714] px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_14px_24px_-18px_rgba(7,23,20,0.9)] transition hover:-translate-y-0.5 hover:bg-emerald-600 md:inline-flex">
                        {{ \App\Support\HomePageContent::text('actions.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden items-center justify-center rounded-full bg-[#071714] px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_14px_24px_-18px_rgba(7,23,20,0.9)] transition hover:-translate-y-0.5 hover:bg-emerald-600 md:inline-flex">
                        {{ \App\Support\HomePageContent::text('actions.login') }}
                    </a>
                @endauth

                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    aria-label="{{ \App\Support\HomePageContent::text('actions.toggle_menu') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-emerald-200 hover:text-emerald-600 lg:hidden">
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

    <div x-show="open" x-cloak x-transition.opacity.duration.200ms @click.outside="open = false"
        class="border-b border-slate-200 bg-white/95 px-4 pb-4 shadow-xl shadow-slate-900/10 backdrop-blur-xl sm:px-6 lg:hidden">
        <div class="mx-auto grid max-w-7xl gap-2 pt-2 text-sm font-bold text-slate-700">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @click="open = false; active = '{{ $link['href'] }}'"
                    class="rounded-2xl px-4 py-3 transition"
                    :class="active === '{{ $link['href'] }}' ? 'bg-emerald-50 text-emerald-700' : 'hover:bg-slate-100'">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                <a href="{{ route($dashboardRoute) }}"
                    class="mt-2 inline-flex items-center justify-center rounded-2xl bg-[#071714] px-4 py-3 text-white">
                    {{ \App\Support\HomePageContent::text('actions.dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="mt-2 inline-flex items-center justify-center rounded-2xl bg-[#071714] px-4 py-3 text-white">
                    {{ \App\Support\HomePageContent::text('actions.login') }}
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- Navbar spacing because header is fixed --}}
<div class="h-[98px] lg:h-[108px]"></div>
