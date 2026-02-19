@extends('layout.guest')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4 py-10">
        <div class="w-full max-w-4xl">
            <div class="grid overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 lg:grid-cols-2">

                {{-- LEFT: Illustration --}}
                <div class="relative hidden lg:block">
                    {{-- gradient background like sample --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800"></div>

                    {{-- dotted decoration --}}
                    <div class="absolute left-10 top-10 h-20 w-20 opacity-30"
                        style="background-image: radial-gradient(rgba(255,255,255,.5) 1.2px, transparent 1.2px);
                            background-size: 10px 10px;">
                    </div>

                    {{-- illustration container --}}
                    <div class="relative flex h-full items-center justify-center p-10">
                        <img src="{{ asset('images/login-illustration.png') }}" alt="Login Illustration"
                            class="w-full max-w-md drop-shadow-[0_25px_25px_rgba(0,0,0,0.25)]" />
                    </div>
                </div>

                {{-- RIGHT: Form --}}
                <div class="p-7 sm:p-10">
                    <div class="mx-auto w-full max-w-md">
                        <div class="text-center">
                            <div class="text-xl font-extrabold tracking-wide text-slate-900">
                                EDU-<span class="text-red-500">X</span>
                            </div>
                            <h1 class="mt-2 text-2xl font-bold text-slate-900">Welcome Back!</h1>
                            <p class="mt-1 text-sm text-slate-500">
                                Login with email/password or continue with Google.
                            </p>
                        </div>

                        {{-- Flash messages --}}
                        @if (session('success'))
                            <div
                                class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}" class="mt-7 space-y-5">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email"
                                    class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Email ID
                                </label>
                                <div class="mt-2">
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autofocus autocomplete="email"
                                        class="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-slate-900 shadow-sm outline-none
                                           focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                        placeholder="you@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password"
                                    class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Password
                                </label>
                                <div class="relative mt-2">
                                    <input id="password" type="password" name="password" required
                                        autocomplete="current-password"
                                        class="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 pr-20 text-slate-900 shadow-sm outline-none
                                           focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                        placeholder="••••••••">
                                    <button type="button" onclick="togglePass('password', this)"
                                        class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-slate-900">

                                        <!-- Eye Icon (default visible) -->
                                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                               -1.274 4.057-5.065 7-9.542 7
                               -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>

                                        <!-- Eye Off Icon (hidden by default) -->
                                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                               a9.956 9.956 0 012.042-3.368M6.223 6.223A9.953 9.953 0 0112 5
                               c4.477 0 8.268 2.943 9.542 7
                               a9.965 9.965 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3v6m-9 4l18-18" />
                                        </svg>
                                    </button>

                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- forgot --}}
                                @if (Route::has('password.request'))
                                    <div class="mt-3 text-right">
                                        <a href="{{ route('password.request') }}"
                                            class="text-sm font-medium text-slate-500 hover:text-slate-900 hover:underline">
                                            Forgot Password ?
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Remember --}}
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="remember" value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span class="text-sm text-slate-600">Remember me</span>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="mt-2 w-full rounded-full bg-indigo-600 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-200
                                   hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                                Log In
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="my-7 flex items-center gap-3">
                            <div class="h-px w-full bg-slate-200"></div>
                            <span class="text-xs font-semibold text-slate-400">OR</span>
                            <div class="h-px w-full bg-slate-200"></div>
                        </div>

                        {{-- Google --}}
                        <div class="flex items-center justify-center">
                            <a href="{{ route('google.redirect') }}"
                                class="group inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700
                                  shadow-sm hover:-translate-y-0.5 hover:shadow-md transition
                                  focus:outline-none focus:ring-4 focus:ring-slate-200">
                                <svg width="22" height="22" viewBox="0 0 48 48" aria-hidden="true">
                                    <path fill="#FFC107"
                                        d="M43.611 20.083H42V20H24v8h11.303C33.699 32.657 29.217 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                                    <path fill="#FF3D00"
                                        d="M6.306 14.691l6.571 4.819C14.655 16.108 19.003 12 24 12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.344 4.314-17.694 10.691z" />
                                    <path fill="#4CAF50"
                                        d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.191 35.091 26.715 36 24 36c-5.196 0-9.666-3.317-11.291-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                                    <path fill="#1976D2"
                                        d="M43.611 20.083H42V20H24v8h11.303a12.07 12.07 0 0 1-4.084 5.565l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
                                </svg>
                                Continue with Google
                            </a>
                        </div>

                        <p class="mt-7 text-center text-sm text-slate-600">
                            New user?
                            <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">
                                Create account
                            </a>
                        </p>
                    </div>
                </div>

            </div>
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
