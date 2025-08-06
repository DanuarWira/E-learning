@php
// Ambil semua item dari kategori vocabulary yang dipilih.
$allItems = $vocabulary->items;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan: {{ $vocabulary->category }}</title>
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

        .clickable-word {
            cursor: pointer;
            transition: color 0.2s;
        }

        .clickable-word:hover {
            color: #4f46e5;
        }

        .clickable-chunk {
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }

        .clickable-chunk:hover {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
        }

        .translation-chunk {
            transition: opacity 0.3s ease;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body class="bg-neutral-100">

    <div id="lesson-data" data-items='@json($allItems)' class="hidden"></div>

    <main class="flex flex-col md:flex-row h-screen antialiased">
        <aside class="w-full md:w-24 bg-white shadow-lg md:shadow-md flex md:flex-col items-center p-2 md:py-6 no-scrollbar shrink-0">
            <a href="{{ route('lessons.show', $lesson) }}" class="hidden md:block mb-6 text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran">
                <i class="fas fa-times fa-2x"></i>
            </a>

            <div id="side-navigation" class="flex flex-row md:flex-col items-center gap-2 md:gap-3 w-full no-scrollbar">
                @foreach($allItems as $index => $item)
                <button class="side-nav-item w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full text-neutral-600 bg-neutral-200 transition-all duration-200 shrink-0" data-index="{{ $index }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>
        </aside>

        <div class="flex-1 flex flex-col p-4 md:p-8 overflow-y-auto">
            <div class="flex items-center gap-4 mb-4 md:mb-8">
                <div class="w-full bg-neutral-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-green-500 h-4 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <a href="{{ route('lessons.show', $lesson) }}" class="md:hidden text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran">
                    <i class="fas fa-times fa-2x"></i>
                </a>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center">
                <div id="item-container" class="w-full max-w-2xl text-center">

                </div>
            </div>

            <div class="border-t-2 pt-6 mt-8 flex justify-between items-center">
                <button id="prev-button" class="py-3 px-4 sm:px-6 bg-white text-neutral-700 rounded-lg shadow font-semibold hover:bg-neutral-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                    <i class="fas fa-chevron-left mr-2"></i> Sebelumnya
                </button>
                <p id="item-counter" class="text-neutral-500 font-medium text-xs sm:text-sm"></p>
                <button id="next-button" class="py-3 px-4 sm:px-6 bg-indigo-600 text-white rounded-lg shadow font-semibold hover:bg-indigo-700 text-sm sm:text-base">
                    Selanjutnya <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
    </main>

    <script>
        const lessonDataElement = document.getElementById('lesson-data');
        const allItems = JSON.parse(lessonDataElement.dataset.items);
        let currentItemIndex = 0;

        const ui = {
            itemContainer: document.getElementById('item-container'),
            prevButton: document.getElementById('prev-button'),
            nextButton: document.getElementById('next-button'),
            progressBar: document.getElementById('progress-bar'),
            itemCounter: document.getElementById('item-counter'),
            sideNavItems: document.querySelectorAll('.side-nav-item'),
            footerContainer: document.querySelector('.border-t-2')
        };

        function playAudio(urlOrText) {
            window.speechSynthesis.cancel();

            if (urlOrText.startsWith('http')) {
                const audio = new Audio(urlOrText);
                audio.play().catch(e => console.error("Gagal memutar audio:", e));
            } else {

            }
        }

        function renderItem(index) {
            if (index < 0 || index >= allItems.length) return;
            currentItemIndex = index;

            const item = allItems[index];

            ui.itemContainer.innerHTML = '';

            const termDiv = document.createElement('div');
            termDiv.className = 'flex items-center justify-center gap-2 sm:gap-4';

            const termH2 = document.createElement('h2');
            termH2.className = 'text-3xl sm:text-4xl md:text-5xl font-bold text-neutral-800 inline';
            termH2.textContent = item.term;
            termDiv.appendChild(termH2);

            let mediaElement = null;
            if (item.media_url) {
                const fullUrl = `${window.location.origin}${item.media_url}`;

                if (item.media_url.match(/\.(jpeg|jpg|gif|png)$/i)) {
                    mediaElement = document.createElement('img');
                    mediaElement.src = fullUrl;
                    mediaElement.alt = item.term;
                    mediaElement.className = 'mt-6 mx-auto max-h-60 rounded-lg border shadow-sm';

                } else if (item.media_url.match(/\.(mp3|wav|ogg)$/i)) {
                    const speakButton = document.createElement('button');
                    speakButton.title = "Dengarkan Audio";
                    speakButton.className = 'ml-3 text-indigo-600 align-middle';
                    speakButton.innerHTML = '<i class="fas fa-volume-up fa-lg"></i>';
                    speakButton.onclick = () => playAudio(fullUrl);
                    termDiv.appendChild(speakButton);

                } else if (item.media_url.match(/\.(mp4)$/i)) {
                    mediaElement = document.createElement('video');
                    mediaElement.src = fullUrl;
                    mediaElement.controls = true;
                    mediaElement.className = 'mt-6 mx-auto w-full max-w-md rounded-lg border shadow-sm';

                    const speakButton = document.createElement('button');
                }
            } else {

            }

            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'mt-8 p-4 sm:p-6 bg-white rounded-lg shadow-inner text-center';

            const detailsTitle = document.createElement('p');
            detailsTitle.className = 'text-base sm:text-lg text-neutral-500 mb-4';

            const detailsContent = document.createElement('p');
            detailsContent.className = 'text-lg sm:text-xl text-neutral-700 leading-relaxed';

            if (Array.isArray(item.details) && item.details.length > 0 && typeof item.details[0] === 'object') {
                item.details.forEach(detail => {
                    const chunkWrapper = document.createElement('span');

                    const chunkSpan = document.createElement('span');
                    chunkSpan.textContent = detail.chunk + ' ';
                    chunkSpan.className = 'clickable-chunk';

                    const translationSpan = document.createElement('span');
                    translationSpan.textContent = `(${detail.translation}) `;
                    translationSpan.className = 'translation-chunk text-neutral-500 italic hidden';

                    chunkSpan.onclick = () => {
                        translationSpan.classList.toggle('hidden');
                    };

                    chunkWrapper.appendChild(chunkSpan);
                    chunkWrapper.appendChild(translationSpan);
                    detailsContent.appendChild(chunkWrapper);
                });
            } else if (typeof item.details === 'string') {
                detailsContent.textContent = item.details || '-';
            } else {
                detailsContent.textContent = '-';
            }

            detailsDiv.appendChild(detailsContent);

            ui.itemContainer.appendChild(termDiv);
            if (mediaElement) {
                ui.itemContainer.appendChild(mediaElement);
            }
            ui.itemContainer.appendChild(detailsDiv);

            updateUI();
        }

        function updateUI() {
            const progressPercentage = ((currentItemIndex + 1) / allItems.length) * 100;
            ui.progressBar.style.width = `${progressPercentage}%`;
            ui.itemCounter.textContent = `${currentItemIndex + 1} / ${allItems.length}`;
            ui.prevButton.disabled = currentItemIndex === 0;

            if (currentItemIndex === allItems.length - 1) {
                ui.nextButton.textContent = 'Selesai';
                ui.nextButton.classList.add('bg-green-600', 'hover:bg-green-700');
                ui.nextButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                ui.nextButton.innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ml-2"></i>';
                ui.nextButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                ui.nextButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            }
            ui.sideNavItems.forEach((navItem, idx) => {
                navItem.classList.toggle('active', idx === currentItemIndex);
                if (idx === currentItemIndex) {
                    navItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        }
        ui.nextButton.addEventListener('click', () => {
            if (currentItemIndex < allItems.length - 1) {
                renderItem(currentItemIndex + 1);
            } else {
                markItemsAsComplete(allItems.map(item => item.id), 'VocabularyItem');
                window.location.href = "{{ route('lessons.show', $lesson) }}";
            }
        });
        ui.prevButton.addEventListener('click', () => {
            if (currentItemIndex > 0) renderItem(currentItemIndex - 1);
        });
        ui.sideNavItems.forEach(navItem => {
            navItem.addEventListener('click', () => {
                const index = parseInt(navItem.dataset.index, 10);
                renderItem(index);
            });
        });

        if (allItems.length > 0) {
            renderItem(0);
        } else {
            ui.itemContainer.innerHTML = `<p class="text-neutral-500 text-xl">Tidak ada kosakata untuk kategori ini.</p>`;
            ui.footerContainer.style.display = 'none';
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
            }
        }
    </script>
</body>

</html>