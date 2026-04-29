{{-- =========================
    Contact Section
========================= --}}
<section id="contact" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:pb-20">
    <div class="grid gap-8 lg:grid-cols-12">

        {{-- LEFT: Contact Form --}}
        <div data-reveal
            class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-7">
            {{-- Glow --}}
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-indigo-400/10 blur-3xl"></div>

            {{-- Badge --}}
            <span
                class="relative inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-indigo-700 ring-1 ring-indigo-100">
                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                {{ $contactBadge ?? \App\Support\HomePageContent::text('contact.badge') }}
            </span>

            {{-- Title --}}
            <h2 class="relative mt-4 text-3xl font-semibold text-slate-950">
                {{ $contactTitle ?? \App\Support\HomePageContent::text('contact.title') }}
            </h2>

            {{-- Description --}}
            <p class="relative mt-3 text-sm leading-7 text-slate-600">
                {{ $contactDescription ?? \App\Support\HomePageContent::text('contact.description') }}
            </p>

            {{-- Success Message --}}
            @if (session('contact_success'))
                <div
                    class="relative mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('contact_success') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('contact.store') }}" class="relative mt-6 space-y-5">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_name" class="contact-label">
                            {{ \App\Support\HomePageContent::text('contact.form.name') }}
                        </label>
                        <input id="contact_name" name="name" type="text" value="{{ old('name') }}"
                            class="contact-input" placeholder="{{ \App\Support\HomePageContent::text('contact.form.name_placeholder') }}">
                        @error('name')
                            <p class="contact-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="contact-label">
                            {{ \App\Support\HomePageContent::text('contact.form.phone') }}
                        </label>
                        <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}"
                            class="contact-input" placeholder="{{ \App\Support\HomePageContent::text('contact.form.phone_placeholder') }}">
                        @error('phone')
                            <p class="contact-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_email" class="contact-label">
                            {{ \App\Support\HomePageContent::text('contact.form.email') }}
                        </label>
                        <input id="contact_email" name="email" type="email" value="{{ old('email') }}"
                            class="contact-input" placeholder="{{ \App\Support\HomePageContent::text('contact.form.email_placeholder') }}">
                        @error('email')
                            <p class="contact-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_subject" class="contact-label">
                            {{ \App\Support\HomePageContent::text('contact.form.subject') }}
                        </label>
                        <input id="contact_subject" name="subject" type="text" value="{{ old('subject') }}"
                            class="contact-input" placeholder="{{ \App\Support\HomePageContent::text('contact.form.subject_placeholder') }}">
                        @error('subject')
                            <p class="contact-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_message" class="contact-label">
                        {{ \App\Support\HomePageContent::text('contact.form.message') }}
                    </label>
                    <textarea id="contact_message" name="message" rows="5" class="contact-input"
                        placeholder="{{ \App\Support\HomePageContent::text('contact.form.message_placeholder') }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="contact-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                    {{ \App\Support\HomePageContent::text('actions.send_message') }}

                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M13 5l7 7-7 7" />
                    </svg>
                </button>

                @if ($errors->any())
                    <p class="text-xs font-semibold text-red-600">
                        {{ \App\Support\HomePageContent::text('contact.form_error') }}
                    </p>
                @endif
            </form>
        </div>

        {{-- RIGHT: Contact Info Card --}}
        <div data-reveal style="--d:.08s" class="lg:col-span-5">
            <div
                class="group relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-7 text-white shadow-xl">

                {{-- Glow --}}
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-blue-500/20 blur-3xl"></div>

                <div class="relative">
                    {{-- Icon same style as Academic Programs --}}
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md transition duration-300 group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 0 1 18 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>

                    <p class="mt-5 text-[11px] font-extrabold uppercase tracking-[0.24em] text-blue-200">
                        {{ $contactCampusLabel ?? \App\Support\HomePageContent::text('contact.campus_label') }}
                    </p>

                    <h3 class="mt-4 text-2xl font-semibold">
                        {{ $contactCampusTitle ?? \App\Support\HomePageContent::text('contact.campus_title') }}
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-slate-100">
                        {{ $contactCampusText ?? \App\Support\HomePageContent::text('contact.campus_text') }}
                    </p>

                    {{-- Contact Cards --}}
                    <div class="mt-6 space-y-3">
                        @foreach ($contactCards as $card)
                            <article
                                class="group/item relative overflow-hidden rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/[0.14]">
                                <div
                                    class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition duration-300 group-hover/item:opacity-100">
                                </div>

                                <p
                                    class="relative text-[11px] font-extrabold uppercase tracking-[0.22em] text-blue-200">
                                    {{ $card['label'] }}
                                </p>

                                <p class="relative mt-2 text-sm font-semibold text-slate-100">
                                    {{ $card['value'] }}
                                </p>

                                <div
                                    class="relative mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover/item:w-16">
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- CTA --}}
                    <div class="mt-6">
                        @guest
                            <a href="{{ route('login') }}"
                                class="block rounded-2xl border border-white/20 px-4 py-3 text-center text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-white/10">
                                {{ \App\Support\HomePageContent::text('actions.login') }}
                            </a>
                        @else
                            <a href="{{ route($dashboardRoute) }}"
                                class="block rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3 text-center text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                                {{ \App\Support\HomePageContent::text('actions.open_dashboard') }}
                            </a>
                        @endguest
                    </div>

                    <div
                        class="mt-6 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
