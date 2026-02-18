@extends('layout.guest')

@section('title', 'Register')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Create account</h1>
                    <p class="mt-1 text-sm text-slate-600">Register as a student or teacher.</p>

                    {{-- Errors (all) --}}
                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.submit') }}" class="mt-6 space-y-4">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                autocomplete="name"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="Your full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email"
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="you@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700">Role</label>
                            <select id="role" name="role" required
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400">
                                <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student
                                </option>
                                <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <div class="relative mt-1">
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                    placeholder="Create a strong password">
                                <button type="button" onclick="togglePass('password', this)"
                                    class="absolute inset-y-0 right-0 px-3 text-sm text-slate-600 hover:text-slate-900"
                                    aria-label="Toggle password visibility">
                                    Show
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Use at least 8 characters (recommended).</p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm
                                Password</label>
                            <div class="relative mt-1">
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-12 text-slate-900 outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                    placeholder="Re-type your password">
                                <button type="button" onclick="togglePass('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 px-3 text-sm text-slate-600 hover:text-slate-900"
                                    aria-label="Toggle password visibility">
                                    Show
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30">
                            Register
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="my-6 flex items-center gap-3">
                        <div class="h-px w-full bg-slate-200"></div>
                        <span class="text-xs text-slate-500">OR</span>
                        <div class="h-px w-full bg-slate-200"></div>
                    </div>

                    <p class="mt-6 text-center text-sm text-slate-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">
                            Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.textContent = isPass ? 'Hide' : 'Show';
        }
    </script>
@endsection
