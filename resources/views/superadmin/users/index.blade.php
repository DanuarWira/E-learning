<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-neutral-100 font-sans">
    <div x-data="{
        isModalOpen: false, 
        isEditMode: false,
        modalTitle: '',
        formAction: '',
        user: {}
    }">
        <div class="flex h-screen">
            @include('superadmin.sidebar')
            <main class="flex-1 p-6 md:p-10 overflow-y-auto">
                <header class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Manajemen User</h1>
                        <p class="text-neutral-500">Kelola semua user supervisor dan pengguna biasa.</p>
                    </div>
                    <button @click="isModalOpen = true; isEditMode = false; modalTitle = 'Buat User Baru'; formAction = '{{ route('superadmin.users.store') }}'; user = {role: 'user'};" class="bg-indigo-600 text-white py-2 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i> Buat Baru
                    </button>
                </header>

                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif
                @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full text-sm text-left text-neutral-500">
                        <thead class="text-xs text-neutral-500 uppercase bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Instansi</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ $user->instansi->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full" :class="{ 'bg-green-100 text-green-700': '{{$user->role}}' === 'supervisor', 'bg-blue-100 text-blue-700': '{{$user->role}}' === 'user' }">{{ $user->role }}</span></td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <button @click="isModalOpen = true; isEditMode = true; modalTitle = 'Edit User'; formAction = '{{ route('superadmin.users.update', $user) }}'; user = {{ json_encode($user) }};" class="font-medium text-blue-600 hover:underline"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center">Belum ada user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">{{ $users->links('vendor.pagination.tailwind') }}</div>
                </div>
            </main>
        </div>

        <!-- Modal Form -->
        <div x-show="isModalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/20">
            <div @click.away="isModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-lg p-8 m-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-2xl font-bold text-neutral-800 mb-6" x-text="modalTitle"></h2>
                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEditMode">@method('PUT')</template>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Nama Lengkap</label>
                        <input type="text" name="name" x-model="user.name" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Alamat Email</label>
                        <input type="email" name="email" x-model="user.email" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Instansi</label>
                        <select name="instansi_id" x-model="user.instansi_id" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md">
                            <option value="">-- Pilih Instansi --</option>
                            @foreach($instansis as $instansi)
                            <option value="{{ $instansi->id }}">{{ $instansi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Role</label>
                        <select name="role" x-model="user.role" required class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md">
                            <option value="user">User</option>
                            <option value="supervisor">Supervisor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Password</label>
                        <input type="password" name="password" :required="!isEditMode" class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md" :placeholder="isEditMode ? 'Kosongkan jika tidak diubah' : ''">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-neutral-300 rounded-md">
                    </div>

                    <div class="mt-8 pt-5 flex justify-end gap-3 border-t">
                        <button type="button" @click="isModalOpen = false" class="bg-neutral-200 py-2 px-6 rounded-lg">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-6 rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>