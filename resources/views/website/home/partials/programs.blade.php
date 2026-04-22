<section id="programs" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <span
                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                {{ __('home.programs.badge') }}
            </span>
            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950 sm:text-4xl">
                {{ __('home.programs.title') }}
            </h2>
        </div>

        <p class="max-w-xl text-slate-500 {{ $isKhmerLocale ? 'text-[15px] leading-8' : 'text-sm leading-7' }}">
            {{ __('home.programs.description') }}
        </p>
    </div>

    <div class="mt-7 grid gap-4 lg:grid-cols-4">
        @foreach ($programs as $item)
            <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3 2 8l10 5 10-5-10-5z" />
                            <path d="m2 8 10 5 10-5" />
                            <path d="M2 13l10 5 10-5" />
                        </svg>
                    </div>

                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-500">
                        {{ $item['level'] }}
                    </span>
                </div>

                <h3 class="mt-6 text-xl font-semibold text-slate-900">{{ $item['title'] }}</h3>
                <p class="mt-3 text-sm text-slate-600 {{ $isKhmerLocale ? 'leading-8' : 'leading-7' }}">{{ $item['description'] }}</p>
            </article>
        @endforeach
    </div>
</section>
