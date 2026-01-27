{{-- CREATE ACCOUNT KARYAWAN (Modern) --}}
<x-app-layout>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-user-plus text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Buat Akun Login</h1>
                    <p class="mt-1 text-blue-100">Registrasi akun untuk karyawan</p>
                </div>
            </div>
        </div>

        <!-- Employee Info Card -->
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($karyawan->nama, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Karyawan:</p>
                        <p class="font-bold text-gray-900">{{ $karyawan->nama }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">PIN:</p>
                    <p class="font-mono font-bold text-blue-600">{{ $karyawan->pin }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('karyawan.account.store', $karyawan->id) }}" class="p-6 space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                    Alamat Email
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-300 @enderror"
                       placeholder="contoh@email.com"
                       required
                       autofocus>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock text-gray-400 mr-2"></i>
                    Password
                </label>
                <div class="relative">
                    <input id="password"
                           type="password"
                           name="password"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-300 @enderror pr-10"
                           placeholder="Minimal 8 karakter"
                           required>
                    <button type="button"
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @else
                    <p class="mt-2 text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password harus minimal 8 karakter
                    </p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock text-gray-400 mr-2"></i>
                    Konfirmasi Password
                </label>
                <div class="relative">
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10"
                           placeholder="Ketik ulang password"
                           required>
                    <button type="button"
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-shield-alt text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Tips Keamanan:</p>
                        <ul class="space-y-1 text-blue-700">
                            <li>• Gunakan kombinasi huruf besar, kecil, dan angka</li>
                            <li>• Jangan gunakan password yang mudah ditebak</li>
                            <li>• Jangan bagikan password ke orang lain</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="{{ route('karyawan.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
