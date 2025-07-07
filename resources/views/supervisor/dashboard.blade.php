<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Supervisor</title>
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
        <!-- Sidebar Supervisor -->
        <aside class="w-64 flex-shrink-0 bg-white text-neutral-600 p-4 flex flex-col border-r">
            <div class="text-center py-4 mb-4">
                <a href="{{ route('supervisor.dashboard') }}" class="text-2xl font-bold text-neutral-900">Engage<span class="text-indigo-600">Supervisor</span></a>
            </div>
            <nav class="flex-grow">
                <ul class="space-y-2">
                    <li><a href="{{ route('supervisor.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold shadow-md"><i class="fas fa-tachometer-alt fa-fw"></i><span>Dasbor Tim</span></a></li>
                    <!-- Tambahkan menu lain untuk supervisor di sini jika perlu -->
                </ul>
            </nav>
            <div class="pt-4 border-t border-neutral-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-neutral-100">
                        <i class="fas fa-sign-out-alt fa-fw"></i><span>Logout</span>
                    </a>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-neutral-800">Progress Tim Anda</h1>
                <p class="text-neutral-500">Pantau kemajuan belajar semua pengguna di instansi Anda.</p>
            </header>

            <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                <table class="w-full text-sm text-left text-neutral-600">
                    <thead class="text-xs text-neutral-500 uppercase">
                        <tr class="border-b">
                            <th class="px-6 py-3">Nama Pengguna</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Progress Keseluruhan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-neutral-900">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-full bg-neutral-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $user->overall_progress }}%"></div>
                                    </div>
                                    <span class="font-medium text-neutral-700">{{ $user->overall_progress }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center">Tidak ada pengguna di instansi Anda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>