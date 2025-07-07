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
            /* indigo-600 */
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
            /* Warna indigo saat di-hover */
        }
    </style>
</head>

<body class="bg-neutral-100">

    <div id="lesson-data" data-items='@json($allItems)' class="hidden"></div>

    <main class="flex flex-col md:flex-row h-screen antialiased">
        <!-- Side Navigation (Desktop) / Top Navigation (Mobile) -->
        <aside class="w-full md:w-24 bg-white shadow-lg md:shadow-md flex md:flex-col items-center p-2 md:py-6 no-scrollbar shrink-0">
            <!-- Close button for Desktop -->
            <a href="{{ route('lessons.show', $lesson) }}" class="hidden md:block mb-6 text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran">
                <i class="fas fa-times fa-2x"></i>
            </a>
            <!-- Nav items container -->
            <div id="side-navigation" class="flex flex-row md:flex-col items-center gap-2 md:gap-3 w-full no-scrollbar">
                @foreach($allItems as $index => $item)
                <button class="side-nav-item w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full text-neutral-600 bg-neutral-200 transition-all duration-200 shrink-0" data-index="{{ $index }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>
        </aside>

        <!-- Konten Utama Pelajaran -->
        <div class="flex-1 flex flex-col p-4 md:p-8 overflow-y-auto">
            <!-- Top bar for Mobile with close button and progress -->
            <div class="flex items-center gap-4 mb-4 md:mb-8">
                <div class="w-full bg-neutral-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-green-500 h-4 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <a href="{{ route('lessons.show', $lesson) }}" class="md:hidden text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran">
                    <i class="fas fa-times fa-2x"></i>
                </a>
            </div>

            <!-- Konten Interaktif -->
            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="w-full max-w-2xl text-center">
                    <p class="text-base sm:text-lg text-neutral-500 mb-2">Term:</p>

                    <div id="term-container" class="flex items-center justify-center gap-2 sm:gap-4 text-3xl sm:text-4xl md:text-5xl font-bold text-neutral-800">
                        <!-- Konten akan diisi oleh JavaScript -->
                    </div>

                    <div class="mt-6 sm:mt-8 p-4 sm:p-6 bg-white rounded-lg shadow-inner">
                        <p class="text-base sm:text-lg text-neutral-500 mb-2">Details:</p>
                        <p id="item-details" class="text-lg sm:text-xl text-neutral-700"></p>
                    </div>
                </div>
            </div>

            <!-- Tombol Navigasi -->
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

        const termContainer = document.getElementById('term-container');
        const detailsElement = document.getElementById('item-details');
        const prevButton = document.getElementById('prev-button');
        const nextButton = document.getElementById('next-button');
        const progressBar = document.getElementById('progress-bar');
        const itemCounter = document.getElementById('item-counter');
        const sideNavItems = document.querySelectorAll('.side-nav-item');
        let isPlaying = false;

        function playAudio(textToSpeak) {
            if (isPlaying || !('speechSynthesis' in window)) return;

            // Hentikan audio yang sedang berjalan jika ada
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(textToSpeak.replace(/"/g, '')); // Hapus tanda kutip
            utterance.lang = 'en-US';
            isPlaying = true;
            utterance.onend = () => {
                isPlaying = false;
            };
            window.speechSynthesis.speak(utterance);
        }

        function renderItem(index) {
            if (index < 0 || index >= allItems.length) return;

            const item = allItems[index];
            termContainer.innerHTML = '';
            detailsElement.textContent = item.details;
            currentItemIndex = index;

            if (item.term.includes(' vs. ')) {
                const words = item.term.split(' vs. ');
                words.forEach((word, wordIndex) => {
                    const wordSpan = document.createElement('span');
                    wordSpan.textContent = word;
                    wordSpan.classList.add('clickable-word');
                    wordSpan.title = `Dengarkan "${word}"`;
                    wordSpan.onclick = () => playAudio(word);
                    termContainer.appendChild(wordSpan);

                    if (wordIndex < words.length - 1) {
                        const vsSpan = document.createElement('span');
                        vsSpan.textContent = ' vs. ';
                        vsSpan.classList.add('text-neutral-400', 'mx-1', 'sm:mx-2', 'text-2xl', 'sm:text-3xl');
                        termContainer.appendChild(vsSpan);
                    }
                });
            } else {
                const termText = document.createElement('h1');
                termText.textContent = item.term;

                const speakButton = document.createElement('button');
                speakButton.title = "Dengarkan Pengucapan";
                speakButton.classList.add('text-neutral-500', 'hover:text-indigo-600', 'transition-colors', 'ml-2');
                speakButton.innerHTML = '<i class="fas fa-volume-up fa-lg"></i>'; // Icon lebih kecil
                speakButton.onclick = () => playAudio(item.term);

                termContainer.appendChild(termText);
                termContainer.appendChild(speakButton);
            }
            updateUI();
        }

        function updateUI() {
            const progressPercentage = ((currentItemIndex + 1) / allItems.length) * 100;
            progressBar.style.width = `${progressPercentage}%`;
            itemCounter.textContent = `${currentItemIndex + 1} / ${allItems.length}`;
            prevButton.disabled = currentItemIndex === 0;

            if (currentItemIndex === allItems.length - 1) {
                nextButton.textContent = 'Selesai';
                nextButton.classList.add('bg-green-600', 'hover:bg-green-700');
                nextButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                nextButton.innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ml-2"></i>';
                nextButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                nextButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            }
            sideNavItems.forEach((navItem, idx) => {
                navItem.classList.remove('active');
                if (idx === currentItemIndex) {
                    navItem.classList.add('active');
                    navItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        }

        nextButton.addEventListener('click', () => {
            if (currentItemIndex < allItems.length - 1) {
                renderItem(currentItemIndex + 1);
            } else {
                markItemsAsComplete(allItems.map(item => item.id), 'VocabularyItem');
                window.location.href = "{{ route('lessons.show', $lesson) }}";
            }
        });
        prevButton.addEventListener('click', () => {
            if (currentItemIndex > 0) renderItem(currentItemIndex - 1);
        });
        sideNavItems.forEach(navItem => {
            navItem.addEventListener('click', () => {
                const index = parseInt(navItem.dataset.index, 10);
                renderItem(index);
            });
        });

        if (allItems.length > 0) {
            renderItem(0);
        } else {
            document.querySelector('.flex-1.flex.flex-col.items-center.justify-center').innerHTML = `<p class="text-neutral-500 text-xl">Tidak ada kosakata untuk kategori ini.</p>`;
            prevButton.parentElement.style.display = 'none';
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