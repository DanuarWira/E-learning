<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadmin</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-neutral-100">
    <div class="flex h-screen bg-neutral-100">
        <!-- Sidebar -->
        @include('superadmin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-800">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="text-neutral-500">Berikut adalah ringkasan dari platform Anda.</p>
            </header>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card User -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500">Jumlah User</p>
                        <p class="mt-1 text-3xl font-semibold text-neutral-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                </div>
                <!-- Card Supervisor -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500">Jumlah Supervisor</p>
                        <p class="mt-1 text-3xl font-semibold text-neutral-900">{{ $stats['total_supervisors'] }}</p>
                    </div>
                    <div class="bg-green-100 text-green-600 p-3 rounded-full">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                </div>
                <!-- Card Instansi -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500">Jumlah Instansi</p>
                        <p class="mt-1 text-3xl font-semibold text-neutral-900">{{ $stats['total_instansi'] }}</p>
                    </div>
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
                <!-- Card Modul -->
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-neutral-500">Jumlah Modul</p>
                        <p class="mt-1 text-3xl font-semibold text-neutral-900">{{ $stats['total_modules'] }}</p>
                    </div>
                    <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Konten lain bisa ditambahkan di sini -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-neutral-800">Area Konten Tambahan</h2>
                <p class="mt-2 text-neutral-600">Anda bisa menambahkan tabel, grafik, atau ringkasan lain di sini.</p>
            </div>
        </main>
    </div>
</body>

</html>