@extends('layout.guest')
@section('title', 'Login')
@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
                    <p class="mt-1 text-sm text-slate-600">Login with email/password or continue with Google.</p>

                    @if (session('success'))
                        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}" class="mt-6 space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="email"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="you@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>

                            <div class="relative mt-1">
                                <input id="password" type="password" name="password" required
                                    autocomplete="current-password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 px-3 text-sm text-slate-600 hover:text-slate-900"
                                    aria-label="Toggle password visibility">
                                    Show
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="remember" value="1"
                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
                                    {{ old('remember') ? 'checked' : '' }}>
                                Remember me
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm font-medium text-slate-900 hover:underline">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30">
                            Login
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="my-6 flex items-center gap-3">
                        <div class="h-px w-full bg-slate-200"></div>
                        <span class="text-xs text-slate-500">OR</span>
                        <div class="h-px w-full bg-slate-200"></div>
                    </div>

                    {{-- Social Login Section --}}
                    <div class="mt-6 flex items-center justify-center gap-4">

                        {{-- Google --}}
                        <a href="{{ route('google.redirect') }}" title="Login with Google"
                            class="group w-12 h-12 flex items-center justify-center rounded-full bg-white border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition
              focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">

                            <svg width="24" height="24" viewBox="0 0 48 48" aria-hidden="true">
                                <path fill="#FFC107"
                                    d="M43.611 20.083H42V20H24v8h11.303C33.699 32.657 29.217 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                                <path fill="#FF3D00"
                                    d="M6.306 14.691l6.571 4.819C14.655 16.108 19.003 12 24 12c3.059 0 5.842 1.154 7.957 3.043l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.344 4.314-17.694 10.691z" />
                                <path fill="#4CAF50"
                                    d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.191 35.091 26.715 36 24 36c-5.196 0-9.666-3.317-11.291-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                                <path fill="#1976D2"
                                    d="M43.611 20.083H42V20H24v8h11.303a12.07 12.07 0 0 1-4.084 5.565l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
                            </svg>
                        </a>

                        {{-- Facebook (Disabled) --}}
                        <button type="button" title="Facebook login coming soon" disabled
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-100 border border-slate-200 opacity-50 cursor-not-allowed">
                            <svg fill="#1877F2" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M22 12a10 10 0 1 0-11.563 9.878v-6.987H8.078v-2.891h2.359V9.845c0-2.325 1.385-3.609 3.507-3.609.997 0 2.042.178 2.042.178v2.25h-1.15c-1.134 0-1.488.704-1.488 1.425v1.711h2.531l-.405 2.891h-2.126v6.987A10.002 10.002 0 0 0 22 12z" />
                            </svg>
                        </button>

                        {{-- Github (Disabled) --}}
                        <button type="button" title="Github login coming soon" disabled
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-100 border border-slate-200 opacity-50 cursor-not-allowed">
                            <svg fill="#000000" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12 0C5.37 0 0 5.373 0 12c0 5.303 3.438 9.8 8.205 11.387.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.547-1.387-1.335-1.756-1.335-1.756-1.09-.745.083-.729.083-.729 1.205.084 1.84 1.24 1.84 1.24 1.07 1.835 2.807 1.305 3.492.997.108-.775.418-1.305.76-1.605-2.665-.304-5.467-1.333-5.467-5.933 0-1.31.467-2.382 1.235-3.222-.123-.303-.536-1.527.117-3.176 0 0 1.008-.322 3.3 1.23a11.49 11.49 0 0 1 3.003-.404c1.02.005 2.047.138 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.649.242 2.873.12 3.176.77.84 1.233 1.912 1.233 3.222 0 4.61-2.807 5.625-5.48 5.921.43.37.823 1.102.823 2.222 0 1.604-.014 2.896-.014 3.286 0 .321.216.694.825.576C20.565 21.796 24 17.299 24 12c0-6.627-5.373-12-12-12z" />
                            </svg>
                        </button>

                    </div>

                    <p class="mt-6 text-center text-sm text-slate-600">
                        New user?
                        <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">
                            Create account
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = event.currentTarget;
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.textContent = isPass ? 'Hide' : 'Show';
        }
    </script>
@endsection
