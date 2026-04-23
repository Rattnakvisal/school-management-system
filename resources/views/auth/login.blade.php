@extends('layout.guest')

@section('title', 'Login')

@section('content')
    @php
        $telegramUsername = trim((string) config('services.telegram.bot_username', ''));
    @endphp

    <div
        class="min-h-screen bg-[linear-gradient(135deg,#eef2ff_0%,#f8fafc_45%,#e0e7ff_100%)] px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-4">
        <div
            class="mx-auto flex max-w-5xl overflow-hidden rounded-[34px] border border-white/60 bg-white shadow-[0_30px_80px_rgba(99,102,241,0.16)] lg:h-[calc(100vh-2rem)] lg:max-h-[920px]">
            <section class="flex w-full items-center bg-white lg:w-[46%] lg:min-h-0">
                <div class="mx-auto flex w-full max-w-md flex-col justify-center px-6 py-8 sm:px-10 sm:py-10 lg:px-10 lg:py-6 xl:px-12">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                            class="h-14 w-14 object-contain drop-shadow-[0_16px_34px_rgba(24,80,200,0.22)]" />
                        <div>
                            <p class="text-xl font-extrabold tracking-[-0.03em] text-slate-900">TechBridge</p>
                            <p class="text-xs font-semibold uppercase tracking-[0.26em] text-amber-500">Academy</p>
                        </div>
                    </div>

                    <div class="mt-6 lg:mt-5">
                        <h1 class="font-['Manrope'] text-4xl font-extrabold tracking-[-0.05em] text-slate-900 sm:text-5xl">
                            Welcome Back
                        </h1>
                        <p class="mt-3 max-w-sm text-sm leading-6 text-slate-500">
                            Sign in with Google or use your email or phone number with your password.
                        </p>
                    </div>

                    @if (session('success'))
                        <div
                            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="space-y-1 pl-5 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('google.redirect') }}"
                            class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-[0_14px_30px_rgba(15,23,42,0.06)] transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-[0_20px_36px_rgba(15,23,42,0.10)] focus:outline-none focus:ring-4 focus:ring-indigo-100">
                            <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
                                <path fill="#FFC107"
                                    d="M43.611 20.083H42V20H24v8h11.303C33.699 32.657 29.217 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                                <path fill="#FF3D00"
                                    d="M6.306 14.691l6.571 4.819C14.655 16.108 19.003 12 24 12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.344 4.314-17.694 10.691z" />
                                <path fill="#4CAF50"
                                    d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.191 35.091 26.715 36 24 36c-5.196 0-9.666-3.317-11.291-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                                <path fill="#1976D2"
                                    d="M43.611 20.083H42V20H24v8h11.303a12.07 12.07 0 0 1-4.084 5.565l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
                            </svg>
                            Log in with Google
                        </a>
                    </div>

                    <div class="my-5 flex items-center gap-4 lg:my-4">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Or login with
                            email</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4 lg:space-y-3">
                        @csrf

                        <div>
                            <label for="login" class="block text-sm font-semibold text-slate-700">
                                Email Address or Phone Number
                            </label>
                            <input id="login" type="text" name="login" value="{{ old('login', old('email')) }}"
                                required autofocus autocomplete="username"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 lg:py-2.5"
                                placeholder="you@example.com or 012345678">
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                                <span class="text-xs font-medium text-slate-400">Secure access</span>
                            </div>

                            <div class="relative mt-2">
                                <input id="password" type="password" name="password" required
                                    autocomplete="current-password"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 lg:py-2.5"
                                    placeholder="Enter your password">
                                <button type="button" onclick="togglePass('password', this)"
                                    class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-700"
                                    aria-label="Toggle password visibility">
                                    <svg class="eye-icon h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="eye-off-icon hidden h-5 w-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.368M6.223 6.223A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.965 9.965 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3v6m-9 4l18-18" />
                                    </svg>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4 text-sm">
                            <label class="inline-flex items-center gap-2 text-slate-500">
                                <input type="checkbox" name="remember" value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span>Keep me logged in</span>
                            </label>

                            <span class="font-medium text-indigo-500">Forgot your password?</span>
                        </div>

                        @if ($telegramUsername !== '')
                            <div class="rounded-2xl bg-slate-50 px-4 py-2.5 text-xs leading-5 text-slate-500">
                                Google login and phone login use OTP. Link Telegram first by sending
                                <span class="font-semibold text-slate-700">link &lt;your phone number&gt;</span>
                                to
                                <a href="{{ 'https://t.me/' . $telegramUsername }}" target="_blank" rel="noopener"
                                    class="font-semibold text-indigo-600 hover:underline">
                                    {{ '@' . $telegramUsername }}
                                </a>.
                            </div>
                        @endif

                        <button type="submit"
                            class="w-full rounded-xl bg-[linear-gradient(135deg,#4f46e5_0%,#6366f1_100%)] px-6 py-3 text-sm font-bold text-white shadow-[0_20px_40px_rgba(79,70,229,0.30)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_50px_rgba(79,70,229,0.34)] focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            Log In
                        </button>
                    </form>

                    <p class="mt-4 text-center text-sm text-slate-500">
                        Need an account? Contact administrator.
                    </p>
                </div>
            </section>

            <aside
                class="relative hidden overflow-hidden bg-[linear-gradient(180deg,#eef2ff_0%,#e8edff_55%,#e3eafe_100%)] lg:flex lg:w-[54%] lg:min-h-0">
                <div class="absolute inset-y-0 left-0 w-px bg-white/70"></div>
                <div class="absolute right-[-80px] top-[-90px] h-72 w-72 rounded-full bg-white/50 blur-3xl"></div>
                <div class="absolute bottom-[-100px] left-[-60px] h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl"></div>

                <div class="relative flex w-full flex-col justify-between px-8 py-7 xl:px-12 xl:py-9">
                    <div class="flex items-start justify-end">
                        <div class="max-w-xs text-right">
                            <div class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <img src="{{ asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                                    class="h-5 w-5 object-contain" />
                                TechBridge Academy
                            </div>
                            <p class="mt-5 text-sm leading-6 text-slate-500">
                                We&apos;ve got tools to help your students, teachers, and admins stay connected every day.
                            </p>
                            <div class="mt-5">
                                <span
                                    class="inline-flex rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">
                                    Start Learning
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="relative mx-auto flex w-full max-w-xl flex-1 items-end justify-center pt-6">
                        <div
                            class="absolute bottom-8 left-1/2 h-20 w-[72%] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(99,102,241,0.18)_0%,rgba(99,102,241,0)_72%)] blur-2xl">
                        </div>
                        <img src="{{ asset('images/8865364.png') }}" alt="Login illustration"
                            class="relative w-full max-w-lg object-contain drop-shadow-[0_28px_35px_rgba(99,102,241,0.16)] lg:max-h-[48vh] xl:max-h-[54vh]" />
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const eye = btn.querySelector('.eye-icon');
            const eyeOff = btn.querySelector('.eye-off-icon');
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            eye.classList.toggle('hidden');
            eyeOff.classList.toggle('hidden');
        }
    </script>
@endsection
