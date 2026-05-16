{{-- =========================
    FAQ Section
========================= --}}
<section id="faq" class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
    <div class="relative overflow-hidden bg-[#effff9] px-5 py-10 sm:px-8 lg:px-12">
        <div class="pointer-events-none absolute right-8 top-8 hidden grid-cols-6 gap-1.5 text-emerald-300 sm:grid">
            @for ($i = 0; $i < 30; $i++)
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            @endfor
        </div>

        <div class="pointer-events-none absolute -left-16 top-12 h-44 w-44 rounded-full border border-emerald-300/50"></div>
        <div class="pointer-events-none absolute -left-4 top-24 h-64 w-64 rounded-full border border-emerald-200/70"></div>

        <div class="relative grid gap-6 lg:grid-cols-12">
            <div data-reveal class="lg:col-span-8">
                <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                    {{ $faqBadge ?? \App\Support\HomePageContent::text('faq.badge') }}
                </span>

                <h2
                    class="[font-family:Outfit,_sans-serif] mt-3 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-[#10221e] sm:text-4xl">
                    {{ $faqTitle ?? \App\Support\HomePageContent::text('faq.title') }}
                </h2>

                <div class="mt-7 grid gap-3">
                    @foreach ($faqItems as $faq)
                        <details data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="group border border-emerald-900/5 bg-white/75 p-4 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition open:bg-white hover:-translate-y-1 hover:bg-white">
                            <summary class="cursor-pointer list-none text-sm font-bold text-[#10221e]">
                                <span class="flex items-center justify-between gap-4">
                                    <span>{{ $faq['question'] }}</span>

                                    <span
                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 transition duration-300 group-open:rotate-45 group-hover:scale-110 [&_svg]:h-4 [&_svg]:w-4">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 5v14M5 12h14" />
                                        </svg>
                                    </span>
                                </span>
                            </summary>

                            <p class="mt-4 border-t border-emerald-900/10 pt-4 text-sm leading-7 text-slate-600">
                                {{ $faq['answer'] }}
                            </p>
                        </details>
                    @endforeach
                </div>
            </div>

            <aside data-reveal style="--d:.08s" class="lg:col-span-4">
                <div
                    class="group h-full border border-emerald-900/5 bg-white/75 p-6 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition hover:-translate-y-1 hover:bg-white">
                    <span
                        class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 transition group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </span>

                    <p class="mt-5 text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                        {{ $faqHelpLabel ?? \App\Support\HomePageContent::text('faq.more_help_label') }}
                    </p>

                    <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-[#10221e]">
                        {{ $faqHelpTitle ?? \App\Support\HomePageContent::text('faq.more_help_title') }}
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        {{ $faqHelpText ?? \App\Support\HomePageContent::text('faq.more_help_text') }}
                    </p>

                    <a href="#contact"
                        class="mt-6 inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                        {{ \App\Support\HomePageContent::text('actions.open_contact') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
