<section id="admission" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div data-reveal
        class="overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg sm:p-8 lg:p-10">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-100">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    {{ __('home.admission.badge') }}
                </span>

                <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold sm:text-4xl">
                    {{ __('home.admission.title') }}
                </h2>

                <p class="mt-4 max-w-xl text-slate-100 text-sm leading-8 sm:text-base">
                    {{ __('home.admission.description') }}
                </p>

                <div class="home-lift-card mt-6 rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                        {{ __('home.admission.open_intake_label') }}</p>
                    <p class="mt-2 text-2xl font-semibold">{{ __('home.admission.open_intake_title') }}</p>
                    <p class="mt-2 text-sm text-slate-200 leading-7">
                        {{ __('home.admission.open_intake_description') }}
                    </p>
                </div>

                <div class="mt-6">
                    @guest
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-cyan-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-cyan-200">
                            {{ __('home.actions.apply_now') }}
                        </a>
                    @else
                        <a href="{{ route($dashboardRoute) }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-cyan-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-cyan-200">
                            {{ __('home.actions.continue_dashboard') }}
                        </a>
                    @endguest
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:col-span-7">
                @foreach ($steps as $i => $s)
                    <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                        class="home-lift-card rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div
                            class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-cyan-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m5 12 5 5L20 7" />
                            </svg>
                        </div>
                        <p class="mt-4 text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                            {{ __('home.admission.step_label', ['number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT)]) }}
                        </p>
                        <p class="mt-2 text-base font-semibold">{{ $s['title'] }}</p>
                        <p class="mt-2 text-sm text-slate-200 leading-7">{{ $s['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
