<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan untuk: {{ $lesson->title }}</title>
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

        .feedback-correct {
            background-color: #dcfce7;
            border-top-color: #22c55e;
        }

        .feedback-incorrect {
            background-color: #fee2e2;
            border-top-color: #ef4444;
        }

        .word-bank-button:disabled {
            opacity: 0.2;
            cursor: not-allowed;
        }

        .match-item.selected {
            background-color: #4f46e5;
            color: white;
            transform: scale(1.05);
        }

        .match-item.correct {
            background-color: #22c55e;
            color: white;
        }

        .match-item.incorrect {
            background-color: #ef4444;
            color: white;
        }

        .option-button.correct {
            background-color: #22c55e;
            color: white;
        }

        .option-button.incorrect {
            background-color: #ef4444;
            color: white;
        }

        .is-recording {
            animation: pulse 1.5s infinite;
        }

        .word-interactive.revealed {
            cursor: default;
            border-bottom: none;
            padding-bottom: 0;
            color: #3b82f6;
        }

        .category-box {
            min-height: 150px;
        }

        .word-draggable {
            cursor: grab;
        }

        .word-draggable:active {
            cursor: grabbing;
        }

        .drag-over {
            border-style: dashed;
            border-color: #4f46e5;
        }

        .answer-area {
            min-height: 60px;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
    </style>
</head>

<body class="bg-neutral-100">

    <div id="exercises-data" data-exercises='@json($exercises)' class="hidden"></div>

    <main class="flex flex-col md:flex-row h-screen antialiased">
        <aside class="w-full md:w-24 bg-white shadow-lg md:shadow-md flex md:flex-col items-center p-2 md:py-6 no-scrollbar shrink-0">
            <a href="{{ route('lessons.show', $lesson) }}" class="hidden md:block mb-6 text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran"><i class="fas fa-times fa-2x"></i></a>
            <div id="side-navigation" class="flex flex-row md:flex-col items-center gap-2 md:gap-3 w-full overflow-x-auto md:overflow-y-auto no-scrollbar">
                @foreach($exercises as $index => $exercise)
                <button class="side-nav-item w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full text-neutral-600 bg-neutral-200 transition-all duration-200 shrink-0" data-index="{{ $index }}">{{ $index + 1 }}</button>
                @endforeach
            </div>
        </aside>

        <div class="flex-1 flex flex-col p-4 md:p-8 overflow-y-auto">
            <div class="flex items-center gap-4 mb-4 md:mb-8">
                <div class="w-full bg-neutral-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-green-500 h-4 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
                <a href="{{ route('lessons.show', $lesson) }}" class="md:hidden text-neutral-500 hover:text-indigo-600" title="Kembali ke Pelajaran"><i class="fas fa-times fa-2x"></i></a>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center">
                <h2 id="exercise-title" class="text-xl font-bold text-neutral-700 mb-6"></h2>
                <div id="game-container" class="w-full max-w-lg"></div>
            </div>

            <div id="footer-container" class="border-t-2 pt-6 mt-8">
                <footer id="feedback-footer" class="border-t-4 transition-colors duration-300 -mt-2 -mx-6 mb-4">
                    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                        <div id="feedback-text" class="text-lg font-semibold"></div>
                    </div>
                </footer>
                <div class="flex justify-between items-center">
                    <button id="prev-button" class="py-3 px-4 sm:px-6 bg-white text-neutral-700 rounded-lg shadow font-semibold hover:bg-neutral-50 disabled:opacity-50 text-sm sm:text-base"><i class="fas fa-chevron-left mr-2"></i> Sebelumnya</button>
                    <p id="item-counter" class="text-neutral-500 font-medium text-xs sm:text-sm"></p>
                    <button id="check-button" class="py-3 px-8 bg-indigo-600 text-white rounded-lg shadow font-semibold hover:bg-indigo-700 text-sm sm:text-base">Periksa</button>
                    <button id="next-button" class="hidden py-3 px-8 bg-indigo-600 text-white rounded-lg shadow font-semibold hover:bg-indigo-700 text-sm sm:text-base">Selanjutnya <i class="fas fa-chevron-right ml-2"></i></button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const exercisesDataElement = document.getElementById('exercises-data');
        const allExercises = JSON.parse(exercisesDataElement.dataset.exercises);
        let currentExerciseIndex = 0;

        const ui = {
            title: document.getElementById('exercise-title'),
            gameContainer: document.getElementById('game-container'),
            checkButton: document.getElementById('check-button'),
            nextButton: document.getElementById('next-button'),
            prevButton: document.getElementById('prev-button'),
            feedbackFooter: document.getElementById('feedback-footer'),
            feedbackText: document.getElementById('feedback-text'),
            progressBar: document.getElementById('progress-bar'),
            itemCounter: document.getElementById('item-counter'),
            sideNavItems: document.querySelectorAll('.side-nav-item'),
            footerContainer: document.getElementById('footer-container'),
        };

        let selectedItems = {
            item1: null,
            item2: null
        };

        function renderCurrentExercise() {
            if (currentExerciseIndex < 0 || currentExerciseIndex >= allExercises.length) return;

            const exercise = allExercises[currentExerciseIndex];
            const content = exercise.exerciseable;

            if (!content) {
                ui.gameContainer.innerHTML = `<p class="text-center text-red-500">Error: Detail latihan tidak ditemukan.</p>`;
                ui.footerContainer.style.display = 'none';
                return;
            }

            ui.title.textContent = exercise.title;
            ui.feedbackFooter.className = 'border-t-4 transition-colors duration-300 -mt-2 -mx-6 mb-4';
            ui.feedbackText.innerHTML = '';
            ui.checkButton.style.display = 'block';
            ui.nextButton.style.display = 'none';
            ui.footerContainer.style.display = 'block';

            // **PERBAIKAN**: Switch case sekarang menggunakan alias dari 'exerciseable_type'
            switch (exercise.exerciseable_type) {
                case 'multiple_choice_quiz':
                    renderMultipleChoiceQuiz(content);
                    break;
                case 'matching_game':
                    renderMatchingGame(content);
                    break;
                case 'pronunciation_drill':
                    renderPronunciationDrill(content);
                    break;
                case 'translation_match':
                    renderTranslationMatch(content);
                    break;
                case 'silent_letter_hunt':
                    renderSilentLetterHunt(content);
                    break;
                case 'spelling_quiz':
                    renderSpellingQuiz(content);
                    break;
                case 'sound_sorting':
                    renderSoundSorting(content);
                    break;
                case 'sentence_scramble':
                    renderSentenceScramble(content);
                    break;
                default:
                    ui.gameContainer.innerHTML = `<p class="text-center text-red-500">Tipe latihan '${exercise.exerciseable_type}' belum diimplementasikan.</p>`;
                    ui.footerContainer.style.display = 'none';
            }
            updateGlobalUI();
        }

        function renderMultipleChoiceQuiz(content) {
            ui.checkButton.style.display = 'none';
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-6 text-xl">${content.question_text || 'Pilih jawaban yang benar:'}</p>
                    <div id="options-container" class="flex flex-col gap-3">
                        ${(content.options || []).map(opt => `<button class="option-button p-4 border-2 rounded-lg text-lg font-semibold" data-value="${opt}">${opt}</button>`).join('')}
                    </div>
                </div>
            `;

            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const isCorrect = checkAnswer(e.target.dataset.value, content.correct_answer);
                    document.querySelectorAll('.option-button').forEach(b => {
                        b.disabled = true;
                        if (b.dataset.value.toLowerCase() === content.correct_answer.toLowerCase()) {
                            b.classList.add('correct');
                        }
                    });
                    if (!isCorrect) e.target.classList.add('incorrect');
                    showFeedback(isCorrect);
                });
            });
        }

        function renderMatchingGame(content) {
            ui.checkButton.style.display = 'none';
            const pairs = content.pairs || [];
            const items1 = pairs.map(p => p.item1).sort(() => 0.5 - Math.random());
            const items2 = pairs.map(p => p.item2).sort(() => 0.5 - Math.random());

            ui.gameContainer.innerHTML = `
                <p class="text-center text-neutral-600 mb-6">${content.instruction || 'Pasangkan item yang sesuai.'}</p>
                <div class="flex justify-between gap-4">
                    <div id="items1" class="flex flex-col gap-3 w-1/2">
                        ${items1.map(item => `<button class="match-item p-4 border rounded-lg" data-type="item1" data-value="${item}">${item}</button>`).join('')}
                    </div>
                    <div id="items2" class="flex flex-col gap-3 w-1/2">
                        ${items2.map(item => `<button class="match-item p-4 border rounded-lg" data-type="item2" data-value="${item}">${item}</button>`).join('')}
                    </div>
                </div>`;
            document.querySelectorAll('.match-item').forEach(btn => btn.addEventListener('click', selectMatchItem));
        }

        function renderPronunciationDrill(content) {
            ui.checkButton.style.display = 'none'; // Tombol periksa tidak dibutuhkan
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Tekan tombol untuk merekam, lalu ucapkan kalimat di bawah ini.</p>
                    <p class="text-2xl font-semibold bg-white p-6 rounded-lg mb-6">${content.prompt_text}</p>
                    <button id="record-button" class="w-20 h-20 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition-transform transform hover:scale-105">
                        <i id="record-icon" class="fas fa-microphone fa-2x"></i>
                    </button>
                    <p id="transcript" class="mt-4 text-gray-500 min-h-[2em] italic"></p>
                </div>
            `;

            const recordButton = document.getElementById('record-button');
            const recordIcon = document.getElementById('record-icon');
            const transcriptEl = document.getElementById('transcript');

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                recordButton.disabled = true;
                transcriptEl.textContent = 'Maaf, browser Anda tidak mendukung pengenalan suara.';
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US'; // Sesuaikan jika bahasa lain
            recognition.interimResults = false;

            recordButton.addEventListener('click', () => {
                if (recordButton.classList.contains('is-recording')) {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });

            recognition.onstart = () => {
                recordButton.classList.add('is-recording');
                recordIcon.className = 'fas fa-stop fa-2x';
                transcriptEl.textContent = 'Mendengarkan...';
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                transcriptEl.textContent = `Anda mengucapkan: "${transcript}"`;
                const cleanTranscript = transcript.replace(/[.,!?]/g, '').toLowerCase().trim();
                const cleanAnswer = content.prompt_text.replace(/[.,!?]/g, '').toLowerCase().trim();
                showFeedback(cleanTranscript === cleanAnswer, `Jawaban benar: <strong>${content.prompt_text}</strong>`);
            };

            recognition.onend = () => {
                recordButton.classList.remove('is-recording');
                recordIcon.className = 'fas fa-microphone fa-2x';
            };

            recognition.onerror = (event) => {
                transcriptEl.textContent = 'Error pengenalan suara: ' + event.error;
                recordButton.classList.remove('is-recording');
                recordIcon.className = 'fas fa-microphone fa-2x';
            };
        }

        function renderTranslationMatch(content) {
            ui.checkButton.style.display = 'none'; // Pengecekan instan
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-2">Terjemahkan kata berikut:</p>
                    <h2 class="text-4xl font-bold text-gray-800 mb-8">${content.question_word}</h2>
                    <div id="options-container" class="flex flex-col gap-3">
                        ${(content.options || []).map(opt => `<button class="option-button p-4 border-2 rounded-lg text-lg transition-colors" data-value="${opt}">${opt}</button>`).join('')}
                    </div>
                </div>
            `;
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', e => {
                    const isCorrect = checkAnswer(e.target.dataset.value, content.correct_answer);
                    document.querySelectorAll('.option-button').forEach(b => {
                        b.disabled = true;
                        if (checkAnswer(b.dataset.value, content.correct_answer)) {
                            b.classList.add('correct');
                        }
                    });
                    if (!isCorrect) {
                        e.target.classList.add('incorrect');
                    }
                    showFeedback(isCorrect);
                });
            });
        }

        function renderSilentLetterHunt(content) {
            ui.checkButton.style.display = 'none';
            const targetWords = new Map((content.words || []).map(w => [w.word, w.silent_letter_index]));

            const sentenceHTML = content.sentence.split(' ').map(word => {
                const cleanWord = word.replace(/[.,]/g, '');
                if (targetWords.has(cleanWord)) {
                    // Ini adalah kata yang bisa diklik, gunakan <span>
                    return `<span class="word-interactive" data-word="${cleanWord}" data-index="${targetWords.get(cleanWord)}">${word}</span>`;
                }
                // Ini adalah kata biasa
                return `<span>${word}</span>`;
            }).join(' ');

            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Klik pada kata untuk menemukan huruf yang tidak dibunyikan (silent letter).</p>
                    <div class="text-3xl bg-white p-6 rounded-lg leading-relaxed flex flex-wrap gap-x-3 gap-y-2 justify-center">
                        ${sentenceHTML}
                    </div>
                </div>
            `;
            document.querySelectorAll('.word-interactive').forEach(span => span.addEventListener('click', e => revealSilentLetter(e.currentTarget)));
        }

        function renderSpellingQuiz(content) {
            const audioUrl = `${window.location.origin}${content.audio_url}`
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-neutral-600 mb-4">Dengarkan dan ketik apa yang Anda dengar.</p>
                    <button onclick="playAudio('${audioUrl}')" class="mb-6 text-indigo-600">
                        <i class="fas fa-volume-up fa-3x"></i>
                    </button>
                    <input type="text" id="spelling-input" class="w-full p-4 text-center text-2xl border-2 rounded-lg" placeholder="Ketik di sini...">
                </div>`;

            ui.checkButton.onclick = () => {
                const userInput = document.getElementById('spelling-input').value.trim();
                const isCorrect = checkAnswer(userInput, content.correct_answer);
                showFeedback(isCorrect, `Jawaban benar: <strong>${content.correct_answer}</strong>`);
            };
        }

        function renderSoundSorting(content) {
            const shuffledWords = [...(content.words || [])].sort(() => 0.5 - Math.random());

            ui.gameContainer.innerHTML = `
                <p class="text-gray-600 mb-4 text-center">Kelompokkan kata-kata berikut berdasarkan suaranya.</p>
                <div id="word-bank-ss" class="flex flex-wrap gap-3 justify-center mb-8 p-4 bg-gray-100 rounded-lg">
                    ${shuffledWords.map(word => `
                        <div draggable="true" class="word-draggable bg-white p-2 px-4 rounded-lg shadow cursor-grab border" data-word="${word.word}" data-category="${word.category_id}">
                            ${word.word}
                        </div>
                    `).join('')}
                </div>
                <div class="flex justify-around gap-4">
                    ${(content.categories || []).map(cat => `
                        <div class="category-box w-1/2 bg-white p-4 rounded-lg border-2 flex flex-col" data-category-id="${cat.id}">
                            <h3 class="font-bold text-center mb-2 text-gray-700">${cat.name}</h3>
                            <div class="flex-1 space-y-2"></div>
                        </div>
                    `).join('')}
                </div>
            `;

            const draggables = document.querySelectorAll('.word-draggable');
            const dropzones = document.querySelectorAll('.category-box');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => draggable.classList.add('opacity-50'));
                draggable.addEventListener('dragend', () => draggable.classList.remove('opacity-50'));
            });

            dropzones.forEach(zone => {
                zone.addEventListener('dragover', e => {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });
                zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
                zone.addEventListener('drop', e => {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    const draggedWord = document.querySelector('.opacity-50');
                    if (draggedWord) {
                        zone.querySelector('.space-y-2').appendChild(draggedWord);
                    }
                });
            });

            ui.checkButton.onclick = () => {
                let allCorrect = true;
                dropzones.forEach(zone => {
                    const zoneId = zone.dataset.categoryId;
                    const wordsInZone = zone.querySelectorAll('.word-draggable');
                    if (wordsInZone.length === 0 && content.words.every(w => w.category_id !== zoneId)) {
                        // Kategori ini memang kosong, jadi tidak salah
                    } else {
                        wordsInZone.forEach(wordEl => {
                            if (wordEl.dataset.category !== zoneId) {
                                allCorrect = false;
                                wordEl.classList.add('bg-red-200');
                            } else {
                                wordEl.classList.remove('bg-red-200');
                                wordEl.classList.add('bg-green-200');
                            }
                        });
                    }
                });
                showFeedback(allCorrect);
            };
        }

        function renderSentenceScramble(content) {
            const words = content.sentence.split(' ');
            const shuffledWords = [...words].sort(() => 0.5 - Math.random());

            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Susun kalimat berikut menjadi benar.</p>
                    <div id="answer-area" class="w-full answer-area bg-white rounded-lg p-3 border-b-4 flex flex-wrap gap-2 items-center"></div>
                    <div id="word-bank" class="mt-8 flex flex-wrap gap-2 justify-center">
                        ${shuffledWords.map((word, index) => `<button class="word-bank-button p-2 px-4 bg-white border-2 rounded-lg text-lg" data-word="${word}" data-index="${index}">${word}</button>`).join('')}
                    </div>
                </div>
            `;

            document.querySelectorAll('.word-bank-button').forEach(btn => btn.addEventListener('click', moveWordToAnswer));
            ui.checkButton.onclick = () => checkSentenceScrambleAnswer(content.sentence);
        }

        function moveWordToAnswer(event) {
            const targetButton = event.currentTarget;
            const answerArea = document.getElementById('answer-area');
            const newButton = document.createElement('button');
            newButton.textContent = targetButton.dataset.word;
            newButton.dataset.originalIndex = targetButton.dataset.index;
            newButton.className = "answer-area-button p-2 px-4 bg-indigo-100 border-2 border-indigo-300 rounded-lg text-lg";
            newButton.onclick = moveWordToBank;
            answerArea.appendChild(newButton);
            targetButton.disabled = true;
        }

        function moveWordToBank(event) {
            const targetButton = event.currentTarget;
            const originalIndex = targetButton.dataset.originalIndex;
            const wordBankButton = document.querySelector(`.word-bank-button[data-index="${originalIndex}"]`);
            if (wordBankButton) wordBankButton.disabled = false;
            targetButton.remove();
        }

        function checkSentenceScrambleAnswer(correctSentence) {
            const answerArea = document.getElementById('answer-area');
            const userAnswer = Array.from(answerArea.children).map(btn => btn.textContent).join(' ');
            const isCorrect = userAnswer.trim() === correctSentence.trim();
            showFeedback(isCorrect, `Jawaban benar: <strong>${correctSentence}</strong>`);
        }

        function revealSilentLetter(span) {
            if (span.classList.contains('revealed')) return;

            const word = span.dataset.word;
            const silentIndex = parseInt(span.dataset.index, 10);

            const highlightedHTML = word.split('').map((char, index) => {
                return index === silentIndex ?
                    `<span class="highlighted-letter">${char}</span>` :
                    `<span>${char}</span>`;
            }).join('');

            span.innerHTML = highlightedHTML;
            span.classList.add('revealed'); // Class ini akan mengubah warna menjadi biru

            const allInteractiveWords = document.querySelectorAll('.word-interactive');
            const revealedWords = document.querySelectorAll('.word-interactive.revealed');
            if (allInteractiveWords.length === revealedWords.length) {
                showFeedback(true);
            }
        }

        function selectMatchItem(event) {
            const target = event.currentTarget;
            const type = target.dataset.type;
            selectedItems[type] = target;
            document.querySelectorAll(`.match-item[data-type="${type}"]`).forEach(btn => btn.classList.remove('selected'));
            target.classList.add('selected');
            if (selectedItems.item1 && selectedItems.item2) checkMatchingAnswer();
        }

        function checkMatchingAnswer() {
            const item1Value = selectedItems.item1.dataset.value;
            const item2Value = selectedItems.item2.dataset.value;
            const currentExerciseContent = allExercises[currentExerciseIndex].exerciseable;
            const isCorrect = currentExerciseContent.pairs.some(p => (p.item1 === item1Value && p.item2 === item2Value) || (p.item1 === item2Value && p.item2 === item1Value));
            const [item1Button, item2Button] = [selectedItems.item1, selectedItems.item2];

            if (isCorrect) {
                item1Button.classList.add('correct');
                item2Button.classList.add('correct');
                item1Button.disabled = true;
                item2Button.disabled = true;
            } else {
                item1Button.classList.add('incorrect');
                item2Button.classList.add('incorrect');
                setTimeout(() => {
                    item1Button.classList.remove('incorrect');
                    item2Button.classList.remove('incorrect');
                }, 1000);
            }

            setTimeout(() => {
                item1Button.classList.remove('selected');
                item2Button.classList.remove('selected');
                selectedItems = {
                    item1: null,
                    item2: null
                };
            }, isCorrect ? 100 : 1000);

            if (document.querySelectorAll('.match-item.correct').length === currentExerciseContent.pairs.length * 2) {
                showFeedback(true);
            }
        }

        function checkAnswer(userInput, correctAnswer) {
            return userInput.toLowerCase() === correctAnswer.toLowerCase();
        }

        function playAudio(audioUrl) {
            const audio = new Audio(audioUrl);
            audio.play().catch(e => {
                console.error("Gagal memutar audio:", e);
                alert("Tidak dapat memutar audio. Cek console untuk detail.");
            });
        }

        function showFeedback(correct, message = '') {
            ui.feedbackFooter.classList.remove('feedback-correct', 'feedback-incorrect');
            ui.checkButton.style.display = 'none';
            ui.nextButton.style.display = 'block';
            if (correct) {
                ui.feedbackFooter.classList.add('feedback-correct');
                ui.feedbackText.innerHTML = 'Benar!';
            } else {
                ui.feedbackFooter.classList.add('feedback-incorrect');
                ui.feedbackText.innerHTML = message || 'Coba lagi!';
            }
        }

        function updateGlobalUI() {
            const progress = ((currentExerciseIndex + 1) / allExercises.length) * 100;
            ui.progressBar.style.width = `${progress}%`;
            ui.itemCounter.textContent = `${currentExerciseIndex + 1} / ${allExercises.length}`;
            ui.prevButton.disabled = currentExerciseIndex === 0;
            ui.sideNavItems.forEach((navItem, idx) => {
                navItem.classList.toggle('active', idx === currentExerciseIndex);
                if (idx === currentExerciseIndex) navItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            });
            if (currentExerciseIndex === allExercises.length - 1) {
                ui.nextButton.textContent = 'Selesai';
                ui.nextButton.classList.add('bg-green-600');
            } else {
                ui.nextButton.innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ml-2"></i>';
                ui.nextButton.classList.remove('bg-green-600');
            }
        }

        ui.nextButton.addEventListener('click', () => {
            if (currentExerciseIndex < allExercises.length - 1) {
                currentExerciseIndex++;
                renderCurrentExercise();
            } else {
                window.location.href = "{{ route('lessons.show', $lesson) }}";
            }
        });

        ui.prevButton.addEventListener('click', () => {
            if (currentExerciseIndex > 0) {
                currentExerciseIndex--;
                renderCurrentExercise();
            }
        });

        ui.sideNavItems.forEach(navItem => {
            navItem.addEventListener('click', (e) => {
                currentExerciseIndex = parseInt(e.currentTarget.dataset.index, 10);
                renderCurrentExercise();
            });
        });

        if (allExercises && allExercises.length > 0) {
            renderCurrentExercise();
        } else {
            ui.gameContainer.innerHTML = `<p class="text-neutral-500 text-xl">Belum ada latihan untuk pelajaran ini.</p>`;
            ui.footerContainer.style.display = 'none';
        }
    </script>
</body>

</html>