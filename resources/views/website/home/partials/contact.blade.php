{{-- =========================
    Contact Section
========================= --}}
<section id="contact" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:pb-20">
    <div class="relative overflow-hidden bg-[#effff9] px-5 py-10 sm:px-8 lg:px-12">
        <div class="pointer-events-none absolute right-8 top-8 hidden grid-cols-6 gap-1.5 text-emerald-300 sm:grid">
            @for ($i = 0; $i < 30; $i++)
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            @endfor
        </div>

        <div class="pointer-events-none absolute -left-16 top-12 h-44 w-44 rounded-full border border-emerald-300/50"></div>
        <div class="pointer-events-none absolute -left-4 top-24 h-64 w-64 rounded-full border border-emerald-200/70"></div>

        <div class="relative grid gap-6 lg:grid-cols-12">
            <div data-reveal
                class="border border-emerald-900/5 bg-white/75 p-6 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur lg:col-span-7">
                <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                    {{ $contactBadge ?? \App\Support\HomePageContent::text('contact.badge') }}
                </span>

                <h2
                    class="[font-family:Outfit,_sans-serif] mt-3 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-[#10221e] sm:text-4xl">
                    {{ $contactTitle ?? \App\Support\HomePageContent::text('contact.title') }}
                </h2>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                    {{ $contactDescription ?? \App\Support\HomePageContent::text('contact.description') }}
                </p>

                @if (session('contact_success'))
                    <div
                        class="mt-5 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                        {{ session('contact_success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="contact_name" class="contact-label">
                                {{ \App\Support\HomePageContent::text('contact.form.name') }}
                            </label>
                            <input id="contact_name" name="name" type="text" value="{{ old('name') }}"
                                class="contact-input"
                                placeholder="{{ \App\Support\HomePageContent::text('contact.form.name_placeholder') }}">
                            @error('name')
                                <p class="contact-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="contact-label">
                                {{ \App\Support\HomePageContent::text('contact.form.phone') }}
                            </label>
                            <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}"
                                class="contact-input"
                                placeholder="{{ \App\Support\HomePageContent::text('contact.form.phone_placeholder') }}">
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
                                class="contact-input"
                                placeholder="{{ \App\Support\HomePageContent::text('contact.form.email_placeholder') }}">
                            @error('email')
                                <p class="contact-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_subject" class="contact-label">
                                {{ \App\Support\HomePageContent::text('contact.form.subject') }}
                            </label>
                            <input id="contact_subject" name="subject" type="text" value="{{ old('subject') }}"
                                class="contact-input"
                                placeholder="{{ \App\Support\HomePageContent::text('contact.form.subject_placeholder') }}">
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

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                        {{ \App\Support\HomePageContent::text('actions.send_message') }}
                    </button>

                    @if ($errors->any())
                        <p class="text-xs font-semibold text-red-600">
                            {{ \App\Support\HomePageContent::text('contact.form_error') }}
                        </p>
                    @endif
                </form>
            </div>

            <aside data-reveal style="--d:.08s" class="lg:col-span-5">
                <div
                    class="group h-full border border-emerald-900/5 bg-white/75 p-6 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition hover:-translate-y-1 hover:bg-white">
                    <span
                        class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 transition group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 0 1 18 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </span>

                    <p class="mt-5 text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                        {{ $contactCampusLabel ?? \App\Support\HomePageContent::text('contact.campus_label') }}
                    </p>

                    <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-[#10221e]">
                        {{ $contactCampusTitle ?? \App\Support\HomePageContent::text('contact.campus_title') }}
                    </h3>

                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        {{ $contactCampusText ?? \App\Support\HomePageContent::text('contact.campus_text') }}
                    </p>

                    <div class="mt-6 space-y-3">
                        @foreach ($contactCards as $card)
                            <article
                                class="border border-emerald-900/5 bg-white/80 p-4 shadow-[0_16px_36px_-34px_rgba(15,118,110,0.45)] transition hover:-translate-y-1 hover:bg-white">
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                                    {{ $card['label'] }}
                                </p>

                                <p class="mt-2 text-sm font-bold text-[#10221e]">
                                    {{ $card['value'] }}
                                </p>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        @guest
                            <a href="{{ route('login') }}"
                                class="inline-flex w-full items-center justify-center rounded-full border border-emerald-500/20 bg-white/80 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-emerald-700 transition hover:-translate-y-0.5 hover:bg-white">
                                {{ \App\Support\HomePageContent::text('actions.login') }}
                            </a>
                        @else
                            <a href="{{ route($dashboardRoute) }}"
                                class="inline-flex w-full items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                                {{ \App\Support\HomePageContent::text('actions.open_dashboard') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
