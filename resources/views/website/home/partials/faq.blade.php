{{-- =========================
    FAQ Section
========================= --}}
<section id="faq" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="grid gap-8 lg:grid-cols-12">

        {{-- Left FAQ Content --}}
        <div data-reveal
            class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-8">
            {{-- Glow --}}
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-indigo-400/10 blur-3xl"></div>

            {{-- Badge --}}
            <span
                class="relative inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-indigo-700 ring-1 ring-indigo-100">
                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                {{ $faqBadge ?? __('home.faq.badge') }}
            </span>

            {{-- Title --}}
            <h2 class="relative mt-4 text-3xl font-semibold text-slate-950">
                {{ $faqTitle ?? __('home.faq.title') }}
            </h2>

            {{-- FAQ Items --}}
            <div class="relative mt-7 grid gap-3">
                @foreach ($faqItems as $faq)
                    <details data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                        class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm transition duration-300 open:border-indigo-200 open:shadow-md hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md">
                        {{-- Glow --}}
                        <div
                            class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition duration-300 group-hover:opacity-100">
                        </div>

                        <summary class="relative cursor-pointer list-none text-sm font-semibold text-slate-900">
                            <span class="flex items-center justify-between gap-4">
                                <span>{{ $faq['question'] }}</span>

                                {{-- Icon same style as Academic Programs --}}
                                <span
                                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md transition duration-300 group-open:rotate-45 group-hover:scale-110 [&_svg]:h-4 [&_svg]:w-4">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </span>
                            </span>
                        </summary>

                        <p class="relative mt-3 border-t border-slate-200 pt-3 text-sm leading-7 text-slate-600">
                            {{ $faq['answer'] }}
                        </p>

                        <div
                            class="relative mt-4 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

        {{-- Right Help Card --}}
        <div data-reveal style="--d:.08s" class="lg:col-span-4">
            <div
                class="group relative overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-7 text-white shadow-xl">

                {{-- Glow --}}
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-blue-500/20 blur-3xl"></div>

                <div class="relative">
                    {{-- Icon same style as Academic Programs --}}
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md transition duration-300 group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </div>

                    <p class="mt-5 text-[11px] font-extrabold uppercase tracking-[0.24em] text-blue-200">
                        {{ $faqHelpLabel ?? __('home.faq.more_help_label') }}
                    </p>

                    <h3 class="mt-4 text-2xl font-semibold">
                        {{ $faqHelpTitle ?? __('home.faq.more_help_title') }}
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-slate-100">
                        {{ $faqHelpText ?? __('home.faq.more_help_text') }}
                    </p>

                    <a href="#contact"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                        {{ __('home.actions.open_contact') }}

                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </a>

                    <div
                        class="mt-6 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
