@extends('layout.guest')

@section('title', 'Verify OTP')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Verify OTP</h1>
                    <p class="mt-1 text-sm text-slate-600">Enter the code sent to your email.</p>

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

                    <form method="POST" action="{{ route('password.verify.post') }}" class="mt-6 space-y-4">
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
                            Verify Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
