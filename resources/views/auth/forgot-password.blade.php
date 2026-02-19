@extends('layout.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Forgot password</h1>
                    <p class="mt-1 text-sm text-slate-600">Enter your email and we’ll send you a reset link.</p>

                    @if (session('status'))
                        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="you@example.com">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm
                               hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30">
                            Send Reset Link
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-600">
                        Remember your password?
                        <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">
                            Back to login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
