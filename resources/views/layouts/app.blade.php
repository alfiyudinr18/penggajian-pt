<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Penggajian Karyawan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-xl font-bold">
                    <a href="{{ route('penggajian.index') }}">Sistem Penggajian</a>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('karyawan.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                        <i class="fas fa-users"></i> Karyawan
                    </a>
                    <a href="{{ route('kehadiran.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                        <i class="fas fa-calendar-check"></i> Kehadiran
                    </a>
                    <a href="{{ route('kasbon.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                        <i class="fas fa-money-bill-wave"></i> Kasbon
                    </a>
                    <a href="{{ route('penggajian.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                        <i class="fas fa-file-invoice-dollar"></i> Penggajian
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
