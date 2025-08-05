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

        /* Style untuk permainan */
        .feedback-correct {
            background-color: #dcfce7;
            border-top-color: #22c55e;
        }

        .feedback-incorrect {
            background-color: #fee2e2;
            border-top-color: #ef4444;
        }

        .word-bank-button,
        .answer-area-button {
            transition: all 0.2s ease-in-out;
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

        .option-button:hover {
            background-color: #e0e7ff;
        }

        .option-button.correct {
            background-color: #22c55e;
            color: white;
        }

        .option-button.incorrect {
            background-color: #ef4444;
            color: white;
        }

        .revealed-char {
            color: #9ca3af;
            /* Warna abu-abu untuk huruf yang sudah terungkap */
        }

        .highlighted-letter {
            color: #ef4444;
            /* Warna merah untuk silent letter */
            font-weight: bold;
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

        // UI Elements
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

        // State
        let selectedItems = {
            question: null,
            answer: null
        };

        function renderCurrentExercise() {
            if (currentExerciseIndex < 0 || currentExerciseIndex >= allExercises.length) return;

            const exercise = allExercises[currentExerciseIndex];
            ui.title.textContent = exercise.title;
            ui.feedbackFooter.className = 'border-t-4 transition-colors duration-300 -mt-2 -mx-6 mb-4';
            ui.feedbackText.innerHTML = '';
            ui.checkButton.style.display = 'block';
            ui.nextButton.style.display = 'none';

            switch (exercise.type) {
                case 'spelling_quiz':
                    renderSpellingQuiz(exercise.content);
                    break;
                case 'matching_game':
                    renderMatchingGame(exercise.content);
                    break;
                case 'fill_in_the_blank':
                    renderFillInTheBlank(exercise.content);
                    break;
                case 'listening_task':
                    renderListeningTask(exercise.content);
                    break;
                case 'speaking_practice':
                    renderSpeakingPractice(exercise.content);
                    break;
                case 'sentence_scramble':
                    renderSentenceScramble(exercise.content);
                    break;
                case 'translation_match':
                    renderTranslationMatch(exercise.content);
                    break;
                case 'fill_with_options':
                    renderFillWithOptions(exercise.content);
                    break;
                case 'multiple_choice_quiz':
                    renderMultipleChoiceQuiz(exercise.content);
                    break;
                case 'fill_multiple_blanks':
                    renderFillMultipleBlanks(exercise.content);
                    break;
                case 'silent_letter_hunt':
                    renderSilentLetterHunt(exercise.content);
                    break;
                case 'pronunciation_drill':
                    renderPronunciationDrill(exercise.content);
                    break;
                default:
                    ui.gameContainer.innerHTML = `<p class="text-center text-red-500">Tipe latihan '${exercise.type}' belum diimplementasikan.</p>`;
                    ui.footerContainer.style.display = 'none';
            }
            updateGlobalUI();
        }

        function updateGlobalUI() {
            const progress = ((currentExerciseIndex + 1) / allExercises.length) * 100;
            ui.progressBar.style.width = `${progress}%`;
            ui.itemCounter.textContent = `${currentExerciseIndex + 1} / ${allExercises.length}`;
            ui.prevButton.disabled = currentExerciseIndex === 0;

            ui.sideNavItems.forEach((navItem, idx) => {
                navItem.classList.remove('active');
                if (idx === currentExerciseIndex) {
                    navItem.classList.add('active');
                    navItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });

            if (currentExerciseIndex === allExercises.length - 1) {
                ui.nextButton.textContent = 'Selesai';
                ui.nextButton.classList.add('bg-green-600');
            } else {
                ui.nextButton.innerHTML = 'Selanjutnya <i class="fas fa-chevron-right ml-2"></i>';
                ui.nextButton.classList.remove('bg-green-600');
            }
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

        ui.nextButton.addEventListener('click', () => {
            if (currentExerciseIndex < allExercises.length - 1) {
                currentExerciseIndex++;
                renderCurrentExercise();
            } else {
                // Aksi ketika sesi selesai
                window.location.href = "{{ route('lessons.show', $lesson) }}";
            }
        });

        ui.prevButton.addEventListener('click', () => {
            if (currentExerciseIndex > 0) {
                currentExerciseIndex--;
                renderCurrentExercise();
            }
        });

        // --- Game Renderers ---
        function renderSpellingQuiz(content) {
            ui.gameContainer.innerHTML = `<div class="text-center"><p class="text-neutral-600 mb-4">Dengarkan dan ketik apa yang Anda dengar.</p><button onclick="playAudio('${content.audio_url}')" class="mb-6 text-indigo-600"><i class="fas fa-volume-up fa-3x"></i></button><input type="text" id="spelling-input" class="w-full p-4 text-center text-2xl border-2 rounded-lg" placeholder="Ketik di sini..."></div>`;
            ui.checkButton.onclick = () => {
                const userInput = document.getElementById('spelling-input').value.trim();
                const isCorrect = userInput.toLowerCase() === content.correct_answer.toLowerCase();
                showFeedback(isCorrect, `Jawaban benar: <strong>${content.correct_answer}</strong>`);
            };
        }

        function renderMatchingGame(content) {
            ui.checkButton.style.display = 'none';
            const questions = content.pairs.map(p => p.question).sort(() => 0.5 - Math.random());
            const answers = content.pairs.map(p => p.answer).sort(() => 0.5 - Math.random());
            ui.gameContainer.innerHTML = `<p class="text-center text-neutral-600 mb-6">Pasangkan item yang sesuai.</p><div class="flex justify-between gap-4"><div id="questions" class="flex flex-col gap-3 w-1/2">${questions.map(q => `<button class="match-item p-4 border rounded-lg" data-type="question" data-value="${q}">${q}</button>`).join('')}</div><div id="answers" class="flex flex-col gap-3 w-1/2">${answers.map(a => `<button class="match-item p-4 border rounded-lg" data-type="answer" data-value="${a}">${a}</button>`).join('')}</div></div>`;
            document.querySelectorAll('.match-item').forEach(btn => btn.addEventListener('click', selectMatchItem));
        }

        function renderFillInTheBlank(content) {
            ui.gameContainer.innerHTML = `<div class="text-center"><p class="text-neutral-600 mb-4">Isi bagian yang kosong.</p><div class="items-center justify-center text-2xl bg-white p-6 rounded-lg"><span>${content.sentence_parts[0]}</span><input type="text" id="fill-input" class="w-32 mx-2 text-center border-b-2 focus:ring-0 focus:border-indigo-500"><span>${content.sentence_parts[1]}</span></div></div>`;
            ui.checkButton.onclick = () => {
                const userInput = document.getElementById('fill-input').value.trim();
                const isCorrect = userInput.toLowerCase() === content.correct_answer.toLowerCase();
                showFeedback(isCorrect, `Jawaban benar: <strong>${content.correct_answer}</strong>`);
            };
        }

        function renderListeningTask(content) {
            ui.checkButton.style.display = 'none';
            const instruction = content.instruction || 'Dengarkan dan pilih jawaban yang benar.';
            // Tombol audio sekarang memanggil playAudio dengan TEKS, bukan URL
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">${instruction}</p>
                    <button onclick="playAudio('${content.correct_answer}')" class="mb-6 text-indigo-600">
                        <i class="fas fa-volume-up fa-3x"></i>
                    </button>
                    <div id="options-container" class="flex flex-col gap-3">
                        ${content.options.map(opt => `<button class="option-button p-4 border rounded-lg text-lg" data-value="${opt}">${opt}</button>`).join('')}
                    </div>
                </div>
            `;
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const isCorrect = checkAnswer(e.target.dataset.value, content.correct_answer, true);
                    document.querySelectorAll('.option-button').forEach(b => b.disabled = true);
                    e.target.classList.add(isCorrect ? 'correct' : 'incorrect');
                    showFeedback(isCorrect)
                });
            });
        }

        function renderSpeakingPractice(content) {
            ui.checkButton.style.display = 'none';
            ui.nextButton.style.display = 'block';
            ui.gameContainer.innerHTML = `<div class="text-center"><p class="text-neutral-600 mb-4">Ucapkan kalimat di bawah ini.</p><p class="text-2xl font-semibold bg-white p-6 rounded-lg mb-6">${content.prompt_text}</p><button id="record-button" class="w-20 h-20 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600"><i class="fas fa-microphone fa-2x"></i></button></div>`;
            document.getElementById('record-button').onclick = () => alert('Fitur Perekaman Suara akan diimplementasikan di sini!');
        }

        function renderSentenceScramble(content) {
            const words = content.sentence.split(' ');
            const shuffledWords = [...words].sort(() => 0.5 - Math.random());

            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Susun kalimat berikut menjadi benar.</p>
                    <div id="answer-area" class="w-full min-h-[60px] bg-white rounded-lg p-3 border-b-4 flex flex-wrap gap-2 items-center">
                        <!-- Jawaban pengguna akan muncul di sini -->
                    </div>
                    <div id="word-bank" class="mt-8 flex flex-wrap gap-2 justify-center">
                        ${shuffledWords.map((word, index) => `<button class="word-bank-button p-2 px-4 bg-white border-2 rounded-lg text-lg" data-word="${word}" data-index="${index}">${word}</button>`).join('')}
                    </div>
                </div>
            `;

            document.querySelectorAll('.word-bank-button').forEach(btn => btn.addEventListener('click', moveWordToAnswer));
            ui.checkButton.onclick = () => checkSentenceScrambleAnswer(content.sentence);
        }

        function renderTranslationMatch(content) {
            ui.checkButton.style.display = 'none'; // Sembunyikan tombol Periksa
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-2">Terjemahkan kata berikut:</p>
                    <h2 class="text-4xl font-bold text-gray-800 mb-8">${content.question_word}</h2>
                    <div id="options-container" class="flex flex-col gap-3">
                        ${content.options.map(opt => `<button class="option-button p-4 border-2 rounded-lg text-lg transition-colors" data-value="${opt}">${opt}</button>`).join('')}
                    </div>
                </div>
            `;
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', e => {
                    const isCorrect = checkAnswer(e.target.dataset.value, content.correct_answer, true);
                    document.querySelectorAll('.option-button').forEach(b => {
                        b.disabled = true;
                        if (b.dataset.value === content.correct_answer) {
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

        function renderFillWithOptions(content) {
            // Tombol "Periksa" utama tidak dibutuhkan karena pengecekan instan
            ui.checkButton.style.display = 'none';

            // Buat HTML untuk permainan
            ui.gameContainer.innerHTML = `
        <div class="text-center">
            <p class="text-gray-600 mb-6">Pilih kata yang tepat untuk melengkapi kalimat.</p>
            
            <!-- Area Kalimat -->
            <div class="flex items-center justify-center text-2xl md:text-3xl bg-white p-6 rounded-lg mb-8">
                <span>${content.sentence_parts[0]}</span>
                <span>_______</span>
                <span>${content.sentence_parts[1]}</span>
            </div>

            <!-- Area Pilihan Jawaban -->
            <div id="options-container" class="flex flex-wrap justify-center gap-3">
                ${content.options.map(opt => `<button class="option-button p-4 border-2 rounded-lg text-lg font-semibold" data-value="${opt}">${opt}</button>`).join('')}
            </div>
        </div>
    `;

            // Tambahkan event listener ke setiap tombol pilihan
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    // Ambil jawaban pengguna dan jawaban yang benar
                    const userAnswer = e.target.dataset.value;
                    const correctAnswer = content.correct_answer;

                    // Periksa jawabannya
                    const isCorrect = checkAnswer(userAnswer, correctAnswer, true);

                    // Beri feedback visual dan non-aktifkan semua tombol
                    document.querySelectorAll('.option-button').forEach(b => {
                        b.disabled = true; // Non-aktifkan semua pilihan
                        if (b.dataset.value === correctAnswer) {
                            b.classList.add('correct'); // Tandai jawaban benar
                        }
                    });

                    // Jika jawaban pengguna salah, tandai juga pilihan mereka
                    if (!isCorrect) {
                        e.target.classList.add('incorrect');
                    }

                    // Tampilkan footer feedback (Benar/Salah)
                    showFeedback(isCorrect);
                });
            });
        }

        function renderMultipleChoiceQuiz(content) {
            ui.checkButton.style.display = 'none'; // Pengecekan instan
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-6 text-xl">${content.question_text}</p>
                    <div id="options-container" class="flex flex-col gap-3">
                        ${content.options.map(opt => `<button class="option-button p-4 border-2 rounded-lg text-lg font-semibold" data-value="${opt}">${opt}</button>`).join('')}
                    </div>
                </div>
            `;

            document.querySelectorAll('.option-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const isCorrect = checkAnswer(e.target.dataset.value, content.correct_answer, true);

                    document.querySelectorAll('.option-button').forEach(b => {
                        b.disabled = true;
                        // Tandai jawaban yang benar dengan hijau
                        if (b.dataset.value.toLowerCase() === content.correct_answer.toLowerCase()) {
                            b.classList.add('correct');
                        }
                    });

                    // Jika jawaban pengguna salah, tandai juga dengan merah
                    if (!isCorrect) {
                        e.target.classList.add('incorrect');
                    }

                    showFeedback(isCorrect);
                });
            });
        }

        function renderFillMultipleBlanks(content) {
            let sentenceHTML = '';
            content.sentence_parts.forEach((part, index) => {
                sentenceHTML += `<span>${part}</span>`;
                if (index < content.correct_answers.length) {
                    sentenceHTML += `<input type="text" class="fill-input w-32 mx-2 text-center border-b-2 focus:ring-0 focus:border-indigo-500 bg-transparent">`;
                }
            });

            // Ganti div dengan flex menjadi div biasa dengan text-center
            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Lengkapi kalimat berikut.</p>
                    <div class="text-xl md:text-2xl bg-white p-6 rounded-lg leading-loose">
                        ${sentenceHTML}
                    </div>
                </div>
            `;
            ui.checkButton.onclick = () => checkFillMultipleBlanksAnswer(content.correct_answers);
        }

        function renderSilentLetterHunt(content) {
            ui.checkButton.style.display = 'none'; // Pengecekan terjadi saat klik

            // Buat map kata target untuk pencarian cepat
            const targetWords = new Map(content.words.map(w => [w.word, w.silent_letter_index]));

            // Ubah kalimat menjadi HTML interaktif
            const sentenceHTML = content.sentence.split(' ').map(word => {
                const cleanWord = word.replace(/[.,]/g, ''); // Hapus tanda baca untuk pencocokan
                if (targetWords.has(cleanWord)) {
                    return `<button class="word-button px-2 py-1 rounded-md" data-word="${cleanWord}" data-index="${targetWords.get(cleanWord)}">${word}</button>`;
                }
                return `<span>${word}</span>`;
            }).join(' ');

            ui.gameContainer.innerHTML = `
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Klik pada kata untuk menemukan huruf yang tidak dibunyikan (silent letter).</p>
                    <div class="text-3xl bg-white p-6 rounded-lg leading-relaxed">${sentenceHTML}</div>
                </div>
            `;

            // Tambahkan event listener ke setiap tombol kata
            document.querySelectorAll('.word-button').forEach(btn => {
                btn.addEventListener('click', e => revealSilentLetter(e.currentTarget));
            });
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
            recognition.lang = 'en-US';
            recognition.interimResults = false;

            recordButton.addEventListener('click', () => {
                recognition.start();
                recordButton.classList.add('is-recording');
                recordIcon.className = 'fas fa-stop fa-2x';
                transcriptEl.textContent = 'Mendengarkan...';
            });

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                transcriptEl.textContent = `Anda mengucapkan: "${transcript}"`;

                // Hapus tanda baca dari kedua string untuk perbandingan yang lebih baik
                const cleanTranscript = transcript.replace(/[.,!?]/g, '').toLowerCase();
                const cleanAnswer = content.prompt_text.replace(/[.,!?]/g, '').toLowerCase();

                showFeedback(cleanTranscript === cleanAnswer, `Jawaban benar: <strong>${content.prompt_text}</strong>`);
            };

            recognition.onend = () => {
                recordButton.classList.remove('is-recording');
                recordIcon.className = 'fas fa-microphone fa-2x';
            };

            recognition.onerror = (event) => {
                transcriptEl.textContent = 'Error pengenalan suara: ' + event.error;
            };
        }

        /** Logika untuk menampilkan silent letter */
        function revealSilentLetter(button) {
            if (button.classList.contains('revealed')) return;

            const word = button.dataset.word;
            const silentIndex = parseInt(button.dataset.index, 10);

            const highlightedHTML = word.split('').map((char, index) => {
                return index === silentIndex ?
                    `<span class="highlighted-letter">${char}</span>` :
                    `<span>${char}</span>`;
            }).join('');

            button.innerHTML = highlightedHTML;
            button.classList.add('revealed');
            button.disabled = true;

            // Cek apakah semua sudah terungkap
            const allButtons = document.querySelectorAll('.word-button');
            const revealedButtons = document.querySelectorAll('.word-button.revealed');
            if (allButtons.length === revealedButtons.length) {
                showFeedback(true);
            }
        }

        /** Logika untuk memeriksa jawaban Fill Multiple Blanks */
        function checkFillMultipleBlanksAnswer(correctAnswers) {
            const inputs = document.querySelectorAll('.fill-input');
            let allCorrect = true;

            inputs.forEach((input, index) => {
                if (input.value.trim().toLowerCase() !== correctAnswers[index].toLowerCase()) {
                    allCorrect = false;
                    input.classList.add('border-red-500'); // Tandai input yang salah
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500'); // Tandai input yang benar
                }
            });

            if (allCorrect) {
                showFeedback(true);
            } else {
                showFeedback(false, `Coba periksa kembali jawaban Anda.`);
            }
        }

        // --- LOGIC FUNCTIONS ---
        function checkAnswer(userInput, correctAnswer, noFeedbackMessage = false) {
            const isCorrect = userInput.toLowerCase() === correctAnswer.toLowerCase();
            if (!noFeedbackMessage) {
                showFeedback(isCorrect, `Jawaban benar: <strong>${correctAnswer}</strong>`);
            }
            return isCorrect;
        }

        /** Logika untuk memindahkan kata ke area jawaban */
        function moveWordToAnswer(event) {
            const targetButton = event.currentTarget;
            const answerArea = document.getElementById('answer-area');

            const newButton = document.createElement('button');
            newButton.textContent = targetButton.dataset.word;
            newButton.dataset.originalIndex = targetButton.dataset.index;
            newButton.className = "answer-area-button p-2 px-4 bg-indigo-100 border-2 border-indigo-300 rounded-lg text-lg";
            newButton.onclick = moveWordToBank;

            answerArea.appendChild(newButton);
            targetButton.disabled = true; // Non-aktifkan tombol di bank kata
        }

        /** Logika untuk mengembalikan kata ke bank */
        function moveWordToBank(event) {
            const targetButton = event.currentTarget;
            const originalIndex = targetButton.dataset.originalIndex;
            const wordBankButton = document.querySelector(`.word-bank-button[data-index="${originalIndex}"]`);

            if (wordBankButton) {
                wordBankButton.disabled = false; // Aktifkan kembali
            }
            targetButton.remove(); // Hapus dari area jawaban
        }

        /** Logika untuk memeriksa jawaban Sentence Scramble */
        function checkSentenceScrambleAnswer(correctSentence) {
            const answerArea = document.getElementById('answer-area');
            const userAnswer = Array.from(answerArea.children).map(btn => btn.textContent).join(' ');
            const isCorrect = userAnswer.trim() === correctSentence.trim();
            showFeedback(isCorrect, `Jawaban benar: <strong>${correctSentence}</strong>`);
        }

        // --- Game Logic ---
        function selectMatchItem(event) {
            const target = event.currentTarget;
            const type = target.dataset.type;
            selectedItems[type] = target;
            document.querySelectorAll(`.match-item[data-type="${type}"]`).forEach(btn => btn.classList.remove('selected'));
            target.classList.add('selected');
            if (selectedItems.question && selectedItems.answer) checkMatchingAnswer();
        }

        function checkMatchingAnswer() {
            const questionValue = selectedItems.question.dataset.value;
            const answerValue = selectedItems.answer.dataset.value;
            const currentExercise = allExercises[currentExerciseIndex];
            const isCorrect = currentExercise.content.pairs.some(p => p.question === questionValue && p.answer === answerValue);

            if (isCorrect) {
                selectedItems.question.classList.add('correct');
                selectedItems.answer.classList.add('correct');
                selectedItems.question.disabled = true;
                selectedItems.answer.disabled = true;
            } else {
                selectedItems.question.classList.add('incorrect');
                selectedItems.answer.classList.add('incorrect');
                setTimeout(() => {
                    selectedItems.question.classList.remove('incorrect');
                    selectedItems.answer.classList.remove('incorrect');
                }, 1000);
            }

            setTimeout(() => {
                selectedItems.question.classList.remove('selected');
                selectedItems.answer.classList.remove('selected');
                selectedItems = {
                    question: null,
                    answer: null
                };
            }, isCorrect ? 100 : 1000);

            if (document.querySelectorAll('.match-item.correct').length === currentExercise.content.pairs.length * 2) {
                showFeedback(true);
            }
        }

        function playAudio(textToSpeak) {
            if (!('speechSynthesis' in window)) {
                alert('Maaf, browser Anda tidak mendukung fitur suara.');
                return;
            }
            window.speechSynthesis.cancel(); // Hentikan suara yang sedang berjalan
            const utterance = new SpeechSynthesisUtterance(textToSpeak);
            utterance.lang = 'en-US'; // Atur bahasa ke Bahasa Inggris
            window.speechSynthesis.speak(utterance);
        }

        // --- Global Event Listeners ---
        // ui.nextButton.addEventListener('click', () => {
        //     if (currentExerciseIndex < allExercises.length - 1) {
        //         currentExerciseIndex++;
        //         renderCurrentExercise();
        //     } else {
        //         // 1. Kirim semua ID item ke backend untuk dicatat
        //         markItemsAsComplete(allItems.map(item => item.id), 'VocabularyItem');

        //         // 2. Arahkan pengguna kembali ke halaman lesson
        //         window.location.href = "{{ route('lessons.show', $lesson) }}";
        //     }
        // });

        // ui.prevButton.addEventListener('click', () => {
        //     if (currentExerciseIndex > 0) {
        //         currentExerciseIndex--;
        //         renderCurrentExercise();
        //     }
        // });

        ui.sideNavItems.forEach(navItem => {
            navItem.addEventListener('click', (e) => {
                currentExerciseIndex = parseInt(e.currentTarget.dataset.index, 10);
                renderCurrentExercise();
            });
        });

        // --- Initialization ---
        if (allExercises.length > 0) {
            renderCurrentExercise();
        } else {
            ui.gameContainer.innerHTML = `<p class="text-neutral-500 text-xl">Belum ada latihan untuk pelajaran ini.</p>`;
            ui.footerContainer.style.display = 'none';
        }
    </script>
</body>

</html>