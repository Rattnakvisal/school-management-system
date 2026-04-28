{{-- =========================
    Footer Section (White)
========================= --}}
<footer class="relative text-slate-700">

    {{-- Top Glow --}}
    <div class="absolute inset-x-0 top-0 h-40"></div>

    <div class="relative mx-auto max-w-7xl px-6 py-16">

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

            {{-- =========================
                Brand
            ========================= --}}
            <div class="lg:col-span-2">

                <div class="flex items-center gap-3">
                    <img src="{{ $footerLogo ?? asset('images/techbridge-logo-mark.svg') }}" alt="Logo"
                        class="h-14 w-14 object-contain" />

                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">
                            {{ $schoolName }}
                        </h3>

                        <p class="text-sm text-slate-500">
                            {{ $footerTagline ?? __('home.footer.tagline') }}
                        </p>
                    </div>
                </div>

                <p class="mt-5 max-w-xl text-sm leading-7 text-slate-600">
                    {{ $footerDescription ?? __('home.footer.description') }}
                </p>

                {{-- Social Icons --}}
                <div class="mt-6 flex gap-3">
                    @foreach ($socials ?? [] as $social)
                        <a href="{{ $social['link'] }}"
                            class="group flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 shadow-sm transition duration-300 hover:-translate-y-1 hover:bg-gradient-to-br hover:from-indigo-600 hover:to-blue-600 hover:text-white">
                            {!! $social['icon'] !!}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- =========================
                Explore Links
            ========================= --}}
            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-indigo-600">
                    {{ $footerExploreLabel ?? __('home.footer.explore') }}
                </h4>

                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($footerLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}"
                                class="text-slate-600 transition duration-200 hover:translate-x-1 hover:text-indigo-600">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- =========================
                Contact
            ========================= --}}
            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-indigo-600">
                    {{ $footerContactLabel ?? __('home.footer.contact') }}
                </h4>

                <ul class="mt-5 space-y-3 text-sm text-slate-600">
                    @foreach ($footerContacts as $contact)
                        <li class="flex items-start gap-2">

                            {{-- Icon same style --}}
                            <span
                                class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>

                            <span>{{ $contact['value'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>

        {{-- =========================
            Bottom Bar
        ========================= --}}
        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-200 pt-6 sm:flex-row">

            <p class="text-sm text-slate-500">
                © {{ date('Y') }} {{ $schoolName }}.
                {{ $footerCopyright ?? __('home.footer.copyright') }}
            </p>

            <div class="flex gap-4 text-sm text-slate-500">
                <a href="#" class="transition hover:text-indigo-600">Privacy</a>
                <a href="#" class="transition hover:text-indigo-600">Terms</a>
            </div>

        </div>

    </div>
</footer>
