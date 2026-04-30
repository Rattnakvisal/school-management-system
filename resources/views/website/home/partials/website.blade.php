<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'TechBridge Academy' }} | {{ \App\Support\HomePageContent::text('meta.title_suffix') }}
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="website-home [font-family:Manrope,_sans-serif] min-h-screen overflow-x-hidden overflow-y-auto bg-slate-100 text-slate-900 antialiased"
    x-data="websiteHomePage()" @keydown.escape.window="if (showBanner) closeBanner()">
    <div class="relative isolate overflow-x-hidden">
        @include('website.home.partials.header')

        @yield('content')

        @include('website.home.partials.footer')

        @include('website.home.partials.chatbot')
    </div>

    <section x-show="tokenPromptVisible" x-cloak x-transition.opacity.duration.200ms
        class="fixed bottom-5 left-5 z-50 w-[min(calc(100vw-2.5rem),25rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/15 ring-1 ring-slate-100"
        role="dialog" aria-live="polite" aria-labelledby="home-token-title">
        <div class="p-5">
            <div class="flex items-start gap-3">
                <span
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3Z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <h2 id="home-token-title" class="text-base font-bold text-slate-950">
                        {{ \App\Support\HomePageContent::text('token.prompt_title') }}
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{ \App\Support\HomePageContent::text('token.prompt_description') }}
                    </p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-end gap-2">
                <button type="button" @click="skipTokenPrompt()"
                    class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-950">
                    {{ \App\Support\HomePageContent::text('token.dismiss') }}
                </button>
                <button type="button" @click="storePageToken()"
                    class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                    {{ \App\Support\HomePageContent::text('token.accept') }}
                </button>
            </div>
        </div>
    </section>

    <section x-show="tokenAlertVisible" x-cloak x-transition.opacity.duration.200ms
        class="fixed bottom-5 left-5 z-50 w-[min(calc(100vw-2.5rem),23rem)] rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-2xl shadow-slate-900/10"
        role="status" aria-live="polite">
        <div class="flex items-start gap-3">
            <span
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path d="m5 12 5 5L20 7" />
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-sm font-bold">{{ \App\Support\HomePageContent::text('token.success_title') }}</p>
                <p class="mt-1 text-sm leading-6 text-emerald-800">
                    {{ \App\Support\HomePageContent::text('token.success_description') }}
                </p>
            </div>

            <button type="button" @click="closeTokenAlert()"
                class="ml-auto inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-emerald-700 transition hover:bg-emerald-100"
                aria-label="{{ \App\Support\HomePageContent::text('token.close_label') }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </section>

    @vite(['resources/js/website/home/chat-bot.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
