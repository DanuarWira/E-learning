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
    </style>
</head>

<body class="bg-gray-100">

    <div id="material-data" data-items='@json($allItems)' data-type="{{ $material->type }}" class="hidden"></div>

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
        const itemsDataElement = document.getElementById('material-data');
        const allItems = JSON.parse(itemsDataElement.dataset.items);
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

        function playAudio(url) {
            const audio = new Audio(url);
            audio.play().catch(e => console.error("Gagal memutar audio:", e));
        }

        function renderItem(index) {
            if (index < 0 || index >= allItems.length) return;
            currentItemIndex = index;

            const item = allItems[index];

            ui.itemContainer.innerHTML = '';

            const titleH2 = document.createElement('h2');
            titleH2.className = 'text-3xl sm:text-4xl md:text-5xl font-bold text-neutral-800';
            titleH2.textContent = item.title;

            let mediaElement = null;
            if (item.media_url) {
                const fullUrl = `${window.location.origin}${item.media_url}`;

                if (item.media_url.match(/\.(jpeg|jpg|gif|png)$/i)) {
                    mediaElement = document.createElement('img');
                    mediaElement.src = fullUrl;
                    mediaElement.alt = item.title;
                    mediaElement.className = 'mt-6 mx-auto max-h-60 rounded-lg border shadow-sm';
                } else if (item.media_url.match(/\.(mp3|wav|ogg)$/i)) {
                    mediaElement = document.createElement('audio');
                    mediaElement.src = fullUrl;
                    mediaElement.controls = true;
                    mediaElement.className = 'mt-6 mx-auto w-full';
                } else if (item.media_url.match(/\.(mp4)$/i)) {
                    mediaElement = document.createElement('video');
                    mediaElement.src = fullUrl;
                    mediaElement.controls = true;
                    mediaElement.className = 'mt-6 mx-auto w-full max-w-md rounded-lg border shadow-sm';
                }
            }

            const descriptionDiv = document.createElement('div');
            descriptionDiv.className = 'mt-8 p-4 sm:p-6 bg-white rounded-lg shadow-inner text-center';

            const descriptionContent = document.createElement('p');
            descriptionContent.className = 'text-lg sm:text-xl text-neutral-700 leading-relaxed';

            if (Array.isArray(item.description) && item.description.length > 0 && typeof item.description[0] === 'object') {
                item.description.forEach(desc => {
                    const chunkWrapper = document.createElement('span');

                    const chunkDiv = document.createElement('span');
                    chunkDiv.textContent = desc.chunk + ' ';
                    chunkDiv.className = 'clickable-chunk';

                    const translationDiv = document.createElement('span');
                    translationDiv.textContent = `(${desc.translation}) `;
                    translationDiv.className = 'translation-chunk text-neutral-500 italic hidden';

                    chunkDiv.onclick = () => {
                        translationDiv.classList.toggle('hidden');
                    };

                    chunkWrapper.appendChild(chunkDiv);
                    chunkWrapper.appendChild(translationDiv);
                    descriptionContent.appendChild(chunkWrapper);
                });
            } else if (typeof item.description === 'string') {
                descriptionContent.textContent = item.description || '-';
            } else {
                descriptionContent.textContent = '-';
            }

            descriptionDiv.appendChild(descriptionContent);

            ui.itemContainer.appendChild(titleH2);
            if (mediaElement) {
                ui.itemContainer.appendChild(mediaElement);
            }
            ui.itemContainer.appendChild(descriptionDiv);

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
                markItemsAsComplete(allItems.map(item => item.id), 'MaterialItem');
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

        if (allItems && allItems.length > 0) {
            renderItem(0);
        } else {
            ui.itemContainer.innerHTML = `<p class="text-neutral-500 text-xl">Tidak ada materi untuk pelajaran ini.</p>`;
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