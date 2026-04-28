<footer class="border-t border-slate-200 bg-slate-950 text-slate-200">
    <div class="mx-auto max-w-7xl px-6 py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <img src="{{ $footerLogo ?? asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                        class="h-14 w-14 object-contain" />
                    <div>
                        <h3 class="[font-family:Outfit,_sans-serif] text-xl font-semibold text-white">{{ $schoolName }}</h3>
                        <p class="text-sm text-slate-400">{{ $footerTagline ?? __('home.footer.tagline') }}</p>
                    </div>
                </div>

                <p class="mt-5 max-w-xl text-sm leading-8 text-slate-400">
                    {{ $footerDescription ?? __('home.footer.description') }}
                </p>
            </div>

            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-300">
                    {{ $footerExploreLabel ?? __('home.footer.explore') }}</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($footerLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="transition hover:text-white">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-300">
                    {{ $footerContactLabel ?? __('home.footer.contact') }}</h4>
                <ul class="mt-5 space-y-3 text-sm text-slate-400">
                    @foreach ($footerContacts as $contact)
                        <li>{{ $contact['value'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} {{ $schoolName }}. {{ $footerCopyright ?? __('home.footer.copyright') }}
        </div>
    </div>
</footer>
