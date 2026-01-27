<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Payroll App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-900 bg-white">

    <div class="flex min-h-screen">

        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-blue-600 to-indigo-800 overflow-hidden items-center justify-center">

            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

            <div class="relative z-10 text-center px-12">
                <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 shadow-2xl">
                    <i class="fas fa-wallet text-4xl text-white"></i>
                    {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain"> --}}
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">Sistem Penggajian</h1>
                <p class="text-blue-100 text-lg leading-relaxed">
                    Kelola data karyawan, kehadiran, dan penggajian dengan mudah, akurat, dan transparan.
                </p>

                <div class="mt-8 flex justify-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-white opacity-100"></div>
                    <div class="w-2 h-2 rounded-full bg-white opacity-50"></div>
                    <div class="w-2 h-2 rounded-full bg-white opacity-50"></div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-100">

                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12">
                        {{-- <i class="fas fa-wallet text-3xl"></i> --}}
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Sistem Penggajian</h2>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang! 👋</h2>
                    <p class="text-slate-500 mt-2 text-sm">Silakan masukkan akun Anda untuk melanjutkan.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-envelope text-slate-400"></i>
                            </div>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors"
                                placeholder="nama@perusahaan.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input :type="show ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   class="pl-10 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 transition-colors"
                                   placeholder="••••••••">

                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ml-2 text-sm text-slate-600">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                        Masuk Sekarang <i class="fas fa-arrow-right ml-2 mt-0.5"></i>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400">
                        &copy; {{ date('Y') }} {{ 'Sistem Penggajian' }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</body>
</html>
