@extends('layout.guest')

@section('title', 'Register')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4 py-10">
        <div class="w-full max-w-4xl">
            <div class="grid overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 lg:grid-cols-2">

                {{-- LEFT SIDE (Illustration) --}}
                <div class="relative hidden lg:block">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800"></div>

                    {{-- dotted decoration --}}
                    <div class="absolute left-10 top-10 h-20 w-20 opacity-30"
                        style="background-image: radial-gradient(rgba(255,255,255,.5) 1.2px, transparent 1.2px);
                            background-size: 10px 10px;">
                    </div>

                    <div class="relative flex h-full items-center justify-center p-10">
                        <img src="{{ asset('images/login-illustration.png') }}" alt="Register Illustration"
                            class="w-full max-w-md drop-shadow-2xl" />
                    </div>
                </div>

                {{-- RIGHT SIDE (Form) --}}
                <div class="p-7 sm:p-10">
                    <div class="mx-auto w-full max-w-md">

                        <div class="text-center">
                            <div class="text-xl font-extrabold tracking-wide text-slate-900">
                                EDU-<span class="text-red-500">X</span>
                            </div>
                            <h1 class="mt-2 text-2xl font-bold text-slate-900">Create Account</h1>
                            <p class="mt-1 text-sm text-slate-500">
                                New registrations are created as student accounts and require OTP email verification.
                            </p>
                        </div>

                        {{-- Errors --}}
                        @if ($errors->any())
                            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.submit') }}" class="mt-7 space-y-5">
                            @csrf

                            {{-- Name --}}
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Full Name
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="mt-2 w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3
                                          focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                    placeholder="Your full name">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Email Address
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="mt-2 w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3
                                          focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                    placeholder="you@example.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Password
                                </label>
                                <div class="relative mt-2">
                                    <input id="password" type="password" name="password" required
                                        class="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 pr-20
                                              focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                        placeholder="Create strong password">
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
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                                                           a9.956 9.956 0 012.042-3.368M6.223 6.223A9.953 9.953 0 0112 5
                                                           c4.477 0 8.268 2.943 9.542 7
                                                           a9.965 9.965 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3v6m-9 4l18-18" />
                                        </svg>
                                    </button>

                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Confirm Password
                                </label>
                                <div class="relative mt-2">
                                    <input id="password_confirmation" type="password" name="password_confirmation" required
                                        class="w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 pr-20
                                              focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                        placeholder="Re-type password">
                                    <button type="button" onclick="togglePass('password_confirmation', this)"
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
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full rounded-full bg-indigo-600 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-200
                                   hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                                Register
                            </button>
                        </form>

                        <p class="mt-7 text-center text-sm text-slate-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">
                                Login
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
