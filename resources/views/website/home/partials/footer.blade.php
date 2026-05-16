{{-- =========================
    Footer Section
========================= --}}
<footer id="footer" class="mx-auto max-w-7xl px-4 pb-8 pt-4 sm:px-6">
    <div class="relative overflow-hidden bg-[#effff9] px-5 py-10 sm:px-8 lg:px-12">
        <div class="pointer-events-none absolute -right-16 -top-20 h-44 w-44 rounded-full border border-emerald-300/60"></div>
        <div class="pointer-events-none absolute -bottom-24 left-8 h-56 w-56 rounded-full border border-emerald-200/70"></div>
        <div class="pointer-events-none absolute right-10 top-10 hidden grid-cols-6 gap-1.5 text-emerald-300 sm:grid">
            @for ($i = 0; $i < 30; $i++)
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            @endfor
        </div>

        <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,0.85fr)_minmax(0,1fr)]">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full border border-emerald-200 bg-white/80 shadow-[0_16px_36px_-28px_rgba(15,118,110,0.65)]">
                        <img src="{{ $footerLogo ?? asset('images/techbridge-logo-mark.svg') }}" alt="{{ $schoolName }} logo"
                            class="h-12 w-12 object-contain" />
                    </span>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                            {{ $footerTagline ?? \App\Support\HomePageContent::text('footer.tagline') }}
                        </span>
                        <h3 class="mt-1 text-2xl font-extrabold tracking-tight text-[#10221e]">
                            {{ $schoolName }}
                        </h3>
                    </div>
                </div>

                <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600">
                    {{ $footerDescription ?? \App\Support\HomePageContent::text('footer.description') }}
                </p>

                @if (!empty($socials))
                    <div class="mt-6 flex flex-wrap gap-3">
                        @foreach ($socials as $social)
                            <a href="{{ $social['link'] }}"
                                class="group inline-flex h-11 w-11 items-center justify-center rounded-full border border-emerald-100 bg-white/80 text-emerald-600 shadow-[0_16px_30px_-24px_rgba(16,185,129,0.8)] transition hover:-translate-y-1 hover:border-emerald-300 hover:bg-emerald-500 hover:text-white"
                                aria-label="Social link">
                                {!! $social['icon'] !!}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="border border-emerald-900/5 bg-white/75 p-5 shadow-[0_18px_40px_-34px_rgba(15,118,110,0.65)] backdrop-blur">
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-emerald-600">
                    {{ $footerExploreLabel ?? \App\Support\HomePageContent::text('footer.explore') }}
                </h4>

                <ul class="mt-5 space-y-2">
                    @foreach ($footerLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                                class="group flex items-center gap-2 text-sm font-bold text-slate-600 transition hover:text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 transition group-hover:scale-125"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="border border-emerald-900/5 bg-white/75 p-5 shadow-[0_18px_40px_-34px_rgba(15,118,110,0.65)] backdrop-blur">
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-emerald-600">
                    {{ $footerContactLabel ?? \App\Support\HomePageContent::text('footer.contact') }}
                </h4>

                <ul class="mt-5 space-y-3 text-sm text-slate-600">
                    @foreach ($footerContacts as $contact)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-emerald-100 bg-emerald-50 text-emerald-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 0 1 18 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </span>

                            <span>
                                <span class="block text-xs font-extrabold uppercase tracking-[0.14em] text-[#10221e]">
                                    {{ $contact['label'] ?? '' }}
                                </span>
                                <span class="mt-0.5 block leading-6">
                                    {{ $contact['value'] }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="relative mt-8 flex flex-col gap-3 border-t border-emerald-900/10 pt-5 text-xs font-bold text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>{{ $footerCopyright ?? \App\Support\HomePageContent::text('footer.copyright') }}</span>
            <a href="#home" class="inline-flex items-center gap-2 text-emerald-600 transition hover:text-emerald-700">
                Back to top
                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 13V3M4 7l4-4 4 4" />
                </svg>
            </a>
        </div>
    </div>
</footer>
