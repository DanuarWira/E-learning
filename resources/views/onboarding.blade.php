<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang!</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-neutral-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-lg p-8 space-y-6 bg-white rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold text-neutral-800">Selamat Datang, {{ Auth::user()->name }}!</h1>
        <p class="text-neutral-600">Sebelum memulai, kami ingin tahu sedikit tentang kemampuan bahasa Inggris Anda. Apakah Anda sudah memiliki pengetahuan dasar bahasa Inggris untuk perhotelan?</p>

        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <form action="{{ route('onboarding.process') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="skip" value="0">
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg shadow font-semibold hover:bg-indigo-700 transition-colors">
                    Tidak, Saya Pemula
                </button>
            </form>

            <form action="{{ route('onboarding.process') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="skip" value="1">
                <button type="submit" class="w-full bg-neutral-200 text-neutral-800 py-3 px-4 rounded-lg font-semibold hover:bg-neutral-300 transition-colors">
                    Ya, Lewati Materi Dasar
                </button>
            </form>
        </div>
    </div>
</body>

</html>