<footer class="border-t border-slate-200 bg-slate-950 text-slate-200">
    <div class="mx-auto max-w-7xl px-6 py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3">
                    <span
                        class="grid h-12 w-12 place-items-center rounded-2xl bg-white/10 text-cyan-300 ring-1 ring-white/10">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="[font-family:Outfit,_sans-serif] text-xl font-semibold text-white">{{ $schoolName }}</h3>
                        <p class="text-sm text-slate-400">{{ __('home.footer.tagline') }}</p>
                    </div>
                </div>

                <p class="mt-5 max-w-xl text-sm leading-8 text-slate-400">
                    {{ __('home.footer.description') }}
                </p>
            </div>

            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-300">
                    {{ __('home.footer.explore') }}</h4>
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
                    {{ __('home.footer.contact') }}</h4>
                <ul class="mt-5 space-y-3 text-sm text-slate-400">
                    <li>{{ $contactCards[0]['value'] ?? 'Phnom Penh, Cambodia' }}</li>
                    <li>+855 12 345 678</li>
                    <li>info@school.edu.kh</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} {{ $schoolName }}. {{ __('home.footer.copyright') }}
        </div>
    </div>
</footer>
