@extends('layout.guest')

@section('title', 'Verify Registration OTP')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Verify Registration OTP</h1>
                    <p class="mt-1 text-sm text-slate-600">Enter the 6-digit code sent to your email to activate your account.</p>

                    @if (session('status'))
                        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('debug_otp'))
                        <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-700">
                            <strong>Dev OTP:</strong> {{ session('debug_otp') }}
                            <div class="text-xs text-slate-500">(Shown only in local/testing environments)</div>
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

                    <form method="POST" action="{{ route('register.verify.post') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', session('email')) }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="you@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">OTP Code</label>
                            <input type="text" name="otp" value="{{ old('otp') }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="123456">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm
                               hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30">
                            Verify and Activate
                        </button>
                    </form>

                    <form method="POST" action="{{ route('register.resend') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email', session('email')) }}">
                        <button type="submit"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-700 font-semibold
                               hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                            Resend OTP
                        </button>
                    </form>

                    <p class="mt-4 text-center text-sm text-slate-600">
                        Already verified?
                        <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
