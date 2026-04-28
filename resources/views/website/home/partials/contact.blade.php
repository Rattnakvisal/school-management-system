<section id="contact" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:pb-20">
    <div class="grid gap-6 lg:grid-cols-12">
        <div data-reveal class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-7">
            <span
                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                {{ $contactBadge ?? __('home.contact.badge') }}
            </span>

            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                {{ $contactTitle ?? __('home.contact.title') }}
            </h2>

            <p class="mt-3 text-slate-600 text-sm leading-7">
                {{ $contactDescription ?? __('home.contact.description') }}
            </p>

            @if (session('contact_success'))
                <div
                    class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('contact_success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_name"
                            class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                            {{ __('home.contact.form.name') }}
                        </label>
                        <input id="contact_name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                            placeholder="{{ __('home.contact.form.name_placeholder') }}">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone"
                            class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                            {{ __('home.contact.form.phone') }}
                        </label>
                        <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                            placeholder="{{ __('home.contact.form.phone_placeholder') }}">
                        @error('phone')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_email"
                            class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                            {{ __('home.contact.form.email') }}
                        </label>
                        <input id="contact_email" name="email" type="email" value="{{ old('email') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                            placeholder="{{ __('home.contact.form.email_placeholder') }}">
                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_subject"
                            class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                            {{ __('home.contact.form.subject') }}
                        </label>
                        <input id="contact_subject" name="subject" type="text" value="{{ old('subject') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                            placeholder="{{ __('home.contact.form.subject_placeholder') }}">
                        @error('subject')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_message"
                        class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                        {{ __('home.contact.form.message') }}
                    </label>
                    <textarea id="contact_message" name="message" rows="5"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                        placeholder="{{ __('home.contact.form.message_placeholder') }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                        {{ __('home.actions.send_message') }}
                    </button>
                </div>

                @if ($errors->any())
                    <p class="mt-3 text-xs font-semibold text-red-600">
                        {{ __('home.contact.form_error') }}</p>
                @endif
            </form>
        </div>

        <div data-reveal style="--d:.08s" class="lg:col-span-5">
            <div
                class="rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                    {{ $contactCampusLabel ?? __('home.contact.campus_label') }}</p>
                <h3 class="[font-family:Outfit,_sans-serif] mt-4 text-2xl font-semibold">
                    {{ $contactCampusTitle ?? __('home.contact.campus_title') }}</h3>
                <p class="mt-3 text-cyan-100 text-sm leading-7">
                    {{ $contactCampusText ?? __('home.contact.campus_text') }}
                </p>

                <div class="mt-6 space-y-3">
                    @foreach ($contactCards as $card)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="home-lift-card rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-200">
                                {{ $card['label'] }}
                            </p>
                            <p class="mt-2 text-sm font-semibold text-slate-100">{{ $card['value'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 space-y-3">
                    @guest
                        <a href="{{ route('login') }}"
                            class="block rounded-2xl border border-white/20 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/10">
                            {{ __('home.actions.login') }}
                        </a>
                    @else
                        <a href="{{ route($dashboardRoute) }}"
                            class="block rounded-2xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-900 transition hover:bg-cyan-50">
                            {{ __('home.actions.open_dashboard') }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>
