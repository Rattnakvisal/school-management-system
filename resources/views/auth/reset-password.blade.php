@extends('layout.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Reset password</h1>
                    <p class="mt-1 text-sm text-slate-600">Create a new password for your account.</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="you@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">New password</label>
                            <input type="password" name="password" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="••••••••">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                            <input type="password" name="password_confirmation" required
                                class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none
                                      focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400"
                                placeholder="••••••••">
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm
                               hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/30">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
