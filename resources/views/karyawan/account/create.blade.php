<x-app-layout>
<div class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Buat Akun Login Karyawan</h1>
        <p class="text-sm text-gray-600 mt-1">
            Untuk Karyawan: <span class="font-bold text-blue-600">{{ $karyawan->nama }} (PIN: {{ $karyawan->pin }})</span>
        </p>
    </div>

    <form method="POST" action="{{ route('karyawan.account.store', $karyawan->id) }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="email" value="Email Karyawan" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('karyawan.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Batal
            </a>
            <x-primary-button>
                Buat Akun
            </x-primary-button>
        </div>
    </form>
</div>
</x-app-layout>
