@php
$allItems = $material->items;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi: {{ $material->type }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .side-nav-item.active {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            transform: scale(1.1);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Pastikan plugin @tailwindcss/aspect-ratio sudah terinstal untuk rasio video */
    </style>
</head>

<body class="bg-gray-100">

    <div id="material-data" data-items='@json($allItems)' data-type="{{ $material->type }}" class="hidden"></div>

    <main class="flex flex-col md:flex-row h-screen antialiased">
        <!-- Navigasi Samping (Desktop) / Atas (Mobile) -->
        <aside class="w-full md:w-24 bg-white shadow-lg md:shadow-md flex md:flex-col items-center p-2 md:py-6 no-scrollbar shrink-0">
            <a href="{{ route('lessons.show', $lesson) }}" class="hidden md:block mb-6 text-gray-500 hover:text-indigo-600" title="Kembali ke Pelajaran">
                <i class="fas fa-times fa-2x"></i>
            </a>
            <div id="side-navigation" class="flex flex-row md:flex-col items-center gap-2 md:gap-3 w-full overflow-x-auto md:overflow-y-auto no-scrollbar">
                @foreach($allItems as $index => $item)
                <button class="side-nav-item w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full text-gray-600 bg-gray-200 transition-all duration-200 shrink-0" data-index="{{ $index }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>
        </aside>

        <!-- Konten Utama Materi -->
        <div class="flex-1 flex flex-col p-4 md:p-8 overflow-y-auto">
            <div class="flex items-center gap-4 mb-4 md:mb-8">
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-green-500 h-4 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <a href="{{ route('lessons.show', $lesson) }}" class="md:hidden text-gray-500 hover:text-indigo-600" title="Kembali ke Pelajaran"><i class="fas fa-times fa-2x"></i></a>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="w-full max-w-3xl text-center">
                    <h1 id="item-title" class="text-2xl md:text-3xl font-bold text-gray-800 mb-4"></h1>
                    <div id="media-container" class="my-6"></div>
                    <div id="description-container" class="mt-4 p-6 bg-white rounded-lg shadow-inner prose max-w-none">
                        <p id="item-description"></p>
                    </div>
                </div>
            </div>

            <div class="border-t-2 pt-6 mt-8 flex justify-between items-center">
                <button id="prev-button" class="py-3 px-4 sm:px-6 bg-white text-gray-700 rounded-lg shadow font-semibold hover:bg-gray-50 disabled:opacity-50 text-sm sm:text-base"><i class="fas fa-chevron-left mr-2"></i> Sebelumnya</button>
                <p id="item-counter" class="text-gray-500 font-medium text-xs sm:text-sm"></p>
                <button id="next-button" class="py-3 px-4 sm:px-6 bg-indigo-600 text-white rounded-lg shadow font-semibold hover:bg-indigo-700 text-sm sm:text-base">Selanjutnya <i class="fas fa-chevron-right ml-2"></i></button>
            </div>
        </div>
    </main>

    <script>
        const dataElement = document.getElementById('material-data');
        const allItems = JSON.parse(dataElement.dataset.items);
        const materialType = dataElement.dataset.type;
        let currentIndex = 0;

        const ui = {
            title: document.getElementById('item-title'),
            media: document.getElementById('media-container'),
            description: document.getElementById('item-description'),
            prevBtn: document.getElementById('prev-button'),
            nextBtn: document.getElementById('next-button'),
            progressBar: document.getElementById('progress-bar'),
            counter: document.getElementById('item-counter'),
            sideNav: document.querySelectorAll('.side-nav-item'),
            mainContent: document.querySelector('.flex-1.flex.flex-col.items-center.justify-center'),
            footer: document.querySelector('.border-t-2.pt-6.mt-8')
        };

        function playMedia(url) {
            if (isPlaying || !url) return;
            const audio = new Audio(url);
            isPlaying = true;
            audio.play().catch(e => console.error("Gagal memutar audio:", e));
            audio.onended = () => {
                isPlaying = false;
            };
        }

        function renderItem(index) {
            if (index < 0 || index >= allItems.length) return;

            const item = allItems[index];
            ui.title.textContent = item.title || '';
            ui.description.textContent = item.description;
            ui.media.innerHTML = ''; // Kosongkan media container

            // Render media berdasarkan tipe
            if (materialType === 'Gambar' && item.url) {
                const container = document.createElement('div');
                container.className = 'flex flex-col items-center gap-4';

                const image = document.createElement('img');
                image.src = item.url;
                image.alt = item.description;
                image.className = 'max-w-full h-80 rounded-lg shadow-sm';

                const audioButton = document.createElement('button');
                if (item.url) {
                    const image = document.createElement('img');
                    image.src = item.url;
                    image.alt = item.description;
                    image.className = 'max-w-full h-80 rounded-lg shadow-sm';
                    container.appendChild(image);
                }
                if (item.audio_url) {
                    const audio = new Audio(item.audio_url);
                    const audioButton = document.createElement('button');
                    audioButton.className = 'flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200';
                    audioButton.onclick = () => audio.play();
                    audioButton.innerHTML = `<i class="fas fa-volume-up"></i><span class="font-semibold">Dengarkan</span>`;
                    container.appendChild(audioButton);
                }
                ui.media.appendChild(container);
            } else if (materialType === 'Audio' && item.url) {
                ui.media.innerHTML = `<audio controls class="w-full"><source src="${item.url}" type="audio/mpeg"></audio>`;
            } else if (materialType === 'Gambar dengan Audio' && item.url) {
                const container = document.createElement('div');
                container.className = 'flex flex-col items-center gap-4';

                const image = document.createElement('img');
                image.src = item.url;
                image.alt = item.description;
                image.className = 'max-w-full h-80 rounded-lg shadow-sm';

                const audioButton = document.createElement('button');
                audioButton.className = 'flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition-colors';
                audioButton.onclick = () => playAudio(item.title);
                audioButton.innerHTML = `<i class="fas fa-volume-up"></i><span class="font-semibold">${item.title}</span>`;

                container.appendChild(image);
                if (item.title) { // Hanya tampilkan tombol jika ada judul
                    container.appendChild(audioButton);
                }

                ui.media.appendChild(container);
            } else if (materialType === 'Video' && item.url) {
                const youtubeIdMatch = item.url.match(/^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})(?:\S+)?$/);
                const youtubeId = youtubeIdMatch ? youtubeIdMatch[1] : null;
                if (youtubeId) {
                    ui.media.innerHTML = `<div class="aspect-w-16 aspect-h-9"><iframe src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen class="w-full h-100 rounded-lg shadow-sm"></iframe></div>`;
                } else {
                    ui.media.innerHTML = `<a href="${item.url}" target="_blank" class="text-indigo-600 hover:underline">Lihat Video &rarr;</a>`;
                }
            }

            currentIndex = index;
            updateUI();
        }

        function updateUI() {
            const progress = ((currentIndex + 1) / allItems.length) * 100;
            ui.progressBar.style.width = `${progress}%`;
            ui.counter.textContent = `${currentIndex + 1} / ${allItems.length}`;
            ui.prevBtn.disabled = currentIndex === 0;

            if (currentIndex === allItems.length - 1) {
                ui.nextBtn.textContent = 'Selesai';
                ui.nextBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                ui.nextBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                ui.nextBtn.innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ml-2"></i>';
                ui.nextBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                ui.nextBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            }

            ui.sideNav.forEach((navItem, idx) => {
                navItem.classList.remove('active');
                if (idx === currentIndex) {
                    navItem.classList.add('active');
                    navItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        }

        ui.nextBtn.addEventListener('click', () => {
            if (currentIndex < allItems.length - 1) {
                renderItem(currentIndex + 1);
            } else {
                window.location.href = "{{ route('lessons.show', $lesson) }}";
            }
        });

        ui.prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) renderItem(currentIndex - 1);
        });

        ui.sideNav.forEach(navItem => {
            navItem.addEventListener('click', () => {
                const index = parseInt(navItem.dataset.index, 10);
                renderItem(index);
            });
        });

        if (allItems.length > 0) {
            renderItem(0);
        } else {
            ui.mainContent.innerHTML = `<p class="text-gray-500 text-xl">Belum ada item untuk materi ini.</p>`;
            ui.footer.style.display = 'none';
        }

        async function markItemsAsComplete(itemIds, itemType) {
            try {
                await fetch("{{ route('progress.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        items: itemIds,
                        type: itemType
                    })
                });
            } catch (error) {
                console.error('Gagal menyimpan progress:', error);
                // Tetap lanjutkan meskipun gagal, jangan blokir pengguna
            }
        }
    </script>
</body>

</html>