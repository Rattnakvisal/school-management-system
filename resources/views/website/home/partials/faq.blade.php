<section id="faq" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div class="grid gap-6 lg:grid-cols-12">
        <div data-reveal class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-8">
            <span
                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                {{ __('home.faq.badge') }}
            </span>

            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                {{ __('home.faq.title') }}
            </h2>

            <div class="mt-6 grid gap-3">
                @foreach ($faqItems as $faq)
                    <details data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                        class="home-lift-card group rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                        <summary class="cursor-pointer list-none text-sm font-semibold text-slate-900">
                            <span class="flex items-center justify-between gap-4">
                                <span>{{ $faq['question'] }}</span>
                                <span class="rounded-full bg-white p-2 text-slate-500 transition group-open:rotate-45">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </span>
                            </span>
                        </summary>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div data-reveal style="--d:.08s" class="lg:col-span-4">
            <div
                class="home-lift-card rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                    {{ __('home.faq.more_help_label') }}</p>
                <h3 class="[font-family:Outfit,_sans-serif] mt-4 text-2xl font-semibold">
                    {{ __('home.faq.more_help_title') }}
                </h3>
                <p class="mt-4 text-sm leading-7 text-slate-100">
                    {{ __('home.faq.more_help_text') }}
                </p>
                <a href="#contact"
                    class="mt-6 inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-900 transition hover:bg-cyan-50">
                    {{ __('home.actions.open_contact') }}
                </a>
            </div>
        </div>
    </div>
</section>
