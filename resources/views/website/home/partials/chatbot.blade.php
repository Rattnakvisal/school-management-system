<button x-show="top" x-cloak x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="button"
    class="fixed bottom-24 right-5 z-40 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800"
    aria-label="{{ __('home.back_to_top') }}">
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="m18 15-6-6-6 6" />
    </svg>
</button>

<div id="school-chatbot" data-school-name="{{ $schoolName }}"
    data-quick-questions='@json(trans('home.chatbot.quick_questions'))' data-answers='@json($chatbotAnswers)'
    data-thinking="{{ __('home.chatbot.thinking') }}" data-empty-question="{{ __('home.chatbot.empty_question') }}"
    data-fallback-answer="{{ __('home.chatbot.fallback_answer') }}"
    data-welcome="{{ __('home.chatbot.welcome', ['schoolName' => $schoolName]) }}"
    class="fixed bottom-5 right-5 z-50">
    <div
        class="home-chatbot-chip pointer-events-none hidden items-center gap-2 rounded-full px-3 py-2 text-xs font-semibold text-slate-700 md:inline-flex">
        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
        {{ __('home.chatbot.chip', ['schoolName' => $schoolName]) }}
    </div>
    <button id="chatbot-toggle" type="button"
        class="home-chatbot-toggle inline-flex h-16 w-16 items-center justify-center rounded-full text-white shadow-xl transition focus:outline-none focus:ring-4 focus:ring-cyan-200"
        aria-expanded="false" aria-controls="chatbot-panel" aria-label="{{ __('home.chatbot.toggle_label') }}">
        <span class="home-chatbot-toggle__status" aria-hidden="true"></span>
        <span class="home-chatbot-mark text-cyan-50" aria-hidden="true">
            <svg class="h-10 w-10" viewBox="0 0 40 40" fill="none">
                <path class="home-chatbot-mark__bubble"
                    d="M9.6 14.5c0-4.2 3.4-7.6 7.6-7.6h7.8c4.2 0 7.6 3.4 7.6 7.6v6.5c0 4.2-3.4 7.6-7.6 7.6h-1.8l-4 3.8c-.5.5-1.3.1-1.3-.6v-3.2h-.7c-4.2 0-7.6-3.4-7.6-7.6v-6.5Z" />
                <path class="home-chatbot-mark__line" d="M20 10.1V7.5" />
                <circle class="home-chatbot-mark__signal" cx="20" cy="5.7" r="1.35" />
                <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--b" cx="15.7" cy="8.5"
                    r="1.05" />
                <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--c" cx="24.3" cy="8.5"
                    r="1.05" />
                <rect class="home-chatbot-mark__head" x="13" y="12.4" width="14" height="10.8" rx="4.2" />
                <rect class="home-chatbot-mark__face" x="15.2" y="14.4" width="9.6" height="6.1" rx="3" />
                <circle class="home-chatbot-mark__eye" cx="17.9" cy="17.35" r="1.05" />
                <circle class="home-chatbot-mark__eye home-chatbot-mark__eye--right" cx="22.1" cy="17.35"
                    r="1.05" />
                <path class="home-chatbot-mark__mouth" d="M17.7 19.6c.8.65 3.8.65 4.6 0" />
                <path class="home-chatbot-mark__arm" d="M13 17.5h-2.2" />
                <path class="home-chatbot-mark__arm home-chatbot-mark__arm--right" d="M27 17.5h2.2" />
                <path class="home-chatbot-mark__line" d="M17.4 23.2v2.4M22.6 23.2v2.4" />
                <circle class="home-chatbot-mark__core" cx="20" cy="27.2" r="1.5" />
            </svg>
        </span>
    </button>

    <section id="chatbot-panel"
        class="home-chatbot-panel mt-3 hidden w-[min(92vw,360px)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
        aria-live="polite">
        <header class="bg-slate-900 px-4 py-3 text-white">
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-500/15 text-cyan-300 ring-1 ring-white/10">
                    <span class="home-chatbot-mark home-chatbot-mark--sm" aria-hidden="true">
                        <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none">
                            <path class="home-chatbot-mark__bubble"
                                d="M9.6 14.5c0-4.2 3.4-7.6 7.6-7.6h7.8c4.2 0 7.6 3.4 7.6 7.6v6.5c0 4.2-3.4 7.6-7.6 7.6h-1.8l-4 3.8c-.5.5-1.3.1-1.3-.6v-3.2h-.7c-4.2 0-7.6-3.4-7.6-7.6v-6.5Z" />
                            <path class="home-chatbot-mark__line" d="M20 10.1V7.5" />
                            <circle class="home-chatbot-mark__signal" cx="20" cy="5.7" r="1.35" />
                            <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--b" cx="15.7"
                                cy="8.5" r="1.05" />
                            <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--c" cx="24.3"
                                cy="8.5" r="1.05" />
                            <rect class="home-chatbot-mark__head" x="13" y="12.4" width="14" height="10.8"
                                rx="4.2" />
                            <rect class="home-chatbot-mark__face" x="15.2" y="14.4" width="9.6" height="6.1"
                                rx="3" />
                            <circle class="home-chatbot-mark__eye" cx="17.9" cy="17.35" r="1.05" />
                            <circle class="home-chatbot-mark__eye home-chatbot-mark__eye--right" cx="22.1"
                                cy="17.35" r="1.05" />
                            <path class="home-chatbot-mark__mouth" d="M17.7 19.6c.8.65 3.8.65 4.6 0" />
                            <path class="home-chatbot-mark__arm" d="M13 17.5h-2.2" />
                            <path class="home-chatbot-mark__arm home-chatbot-mark__arm--right" d="M27 17.5h2.2" />
                            <path class="home-chatbot-mark__line" d="M17.4 23.2v2.4M22.6 23.2v2.4" />
                            <circle class="home-chatbot-mark__core" cx="20" cy="27.2" r="1.5" />
                        </svg>
                    </span>
                </span>
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-300">
                        {{ __('home.chatbot.assistant_label') }}
                    </p>
                    <h3 class="mt-1 text-sm font-semibold">{{ __('home.chatbot.title') }}</h3>
                </div>
            </div>
        </header>

        <div id="chatbot-messages" class="h-80 space-y-3 overflow-y-auto bg-slate-50 px-3 py-3"></div>

        <div class="border-t border-slate-200 bg-white p-3">
            <div id="chatbot-quick-questions" class="mb-3 flex flex-wrap gap-2"></div>
            <form id="chatbot-form" class="flex items-center gap-2">
                <input id="chatbot-input" type="text"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                    placeholder="{{ __('home.chatbot.input_placeholder') }}" autocomplete="off">
                <button type="submit"
                    class="rounded-xl bg-cyan-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500">
                    {{ __('home.chatbot.send') }}
                </button>
            </form>
        </div>
    </section>
</div>
