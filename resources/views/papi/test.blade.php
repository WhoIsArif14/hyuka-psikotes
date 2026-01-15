<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $alatTes->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12" data-user-id="{{ auth()->user()->email }}" data-test-id="{{ $test->id }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- QUESTION NAVIGATOR SIDEBAR --}}
                <div class="w-full lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 lg:sticky lg:top-6">
                        <div class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-t-xl">
                            <h3 class="font-bold text-white text-base flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Navigasi Item
                            </h3>
                            <p class="text-xs text-purple-100 mt-1">Klik nomor untuk ke item</p>
                        </div>

                        <div class="p-4 lg:max-h-[calc(100vh-300px)] lg:overflow-y-auto">
                            <div class="grid grid-cols-8 sm:grid-cols-10 lg:grid-cols-5 gap-2" id="question-navigator">
                                @for ($i = 1; $i <= $questions->count(); $i++)
                                    <button type="button" onclick="scrollToItem({{ $i }})" data-item-nav="{{ $i }}"
                                        class="aspect-square flex items-center justify-center rounded-lg text-sm font-bold transition-all duration-200 w-full border-2 shadow-sm hover:shadow-md hover:scale-105 bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:border-gray-400">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>

                            {{-- Legend --}}
                            <div class="mt-4 pt-4 border-t-2 border-gray-200 space-y-2.5 text-xs bg-gray-50 -mx-4 px-4 py-3 rounded-b-xl">
                                <p class="font-semibold text-gray-700 mb-2">Keterangan:</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-green-100 border-2 border-green-300 rounded-lg shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Sudah dijawab</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-white border-2 border-gray-300 rounded-lg shadow-sm"></div>
                                    <span class="text-gray-700 font-medium">Belum dijawab</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAIN CONTENT --}}
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">

                    {{-- INFORMASI TES & TIMER --}}
                    <div x-data="timer({{ $timeRemaining }})" x-init="startTimer()"
                        class="mb-8 p-4 border-l-4 border-blue-400 bg-blue-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $alatTes->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Total: {{ $questions->count() }} pasang pernyataan
                                </p>
                            </div>
                            <div id="timer-display" class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="formatTime()"></span>
                            </div>
                        </div>
                        
                        {{-- Violation Counter --}}
                        <div class="mt-3 flex justify-between items-center text-sm">
                            <span class="text-gray-600">
                                ⚠️ Pelanggaran: <span id="violation-count" class="text-red-600 font-bold">0</span> / 3
                            </span>
                            <span class="text-gray-500 text-xs">
                                Screenshot & keluar tab tidak diperbolehkan
                            </span>
                        </div>
                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Progress</span>
                            <span id="progress-percentage">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    {{-- FORM SOAL PAPI --}}
                    <form id="papi-form" method="POST" 
                          action="{{ route('papi.submit', ['test' => $test->id, 'alat_tes' => $alatTes->id]) }}">
                        @csrf
                        
                        <input type="hidden" name="violation_count" id="hidden-violation-count" value="0">
                        <input type="hidden" name="tab_switches" id="hidden-tab-switches" value="0">
                        
                        {{-- PETUNJUK --}}
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 font-semibold">
                                        Pilih SATU pernyataan yang PALING sesuai dengan diri Anda dari setiap pasangan!
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- DAFTAR PASANGAN PERNYATAAN --}}
                        <div class="space-y-6">
                            @foreach($questions as $index => $question)
                                <div class="bg-gray-50 p-6 rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-all" 
                                     data-item="{{ $question->item_number }}">
                                    
                                    {{-- Nomor Item --}}
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-bold text-gray-700">
                                            Item {{ $question->item_number }}
                                        </h3>
                                        <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full">
                                            {{ $index + 1 }} / {{ $questions->count() }}
                                        </span>
                                    </div>

                                    {{-- Opsi A dan B --}}
                                    <div class="space-y-3">
                                        {{-- Pernyataan A --}}
                                        <label class="flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 hover:bg-blue-50 hover:border-blue-400 group option-label">
                                            <input type="radio" 
                                                   name="item_{{ $question->item_number }}" 
                                                   value="A"
                                                   required
                                                   onchange="updateProgress()"
                                                   class="h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500 mt-0.5">
                                            
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-start">
                                                    <span class="font-bold text-blue-600 mr-2">A.</span>
                                                    <span class="text-gray-700 group-hover:text-gray-900">
                                                        {{ $question->statement_a }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>

                                        {{-- Pernyataan B --}}
                                        <label class="flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 hover:bg-green-50 hover:border-green-400 group option-label">
                                            <input type="radio" 
                                                   name="item_{{ $question->item_number }}" 
                                                   value="B"
                                                   required
                                                   onchange="updateProgress()"
                                                   class="h-5 w-5 text-green-600 border-gray-300 focus:ring-green-500 mt-0.5">
                                            
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-start">
                                                    <span class="font-bold text-green-600 mr-2">B.</span>
                                                    <span class="text-gray-700 group-hover:text-gray-900">
                                                        {{ $question->statement_b }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Error Message per Item --}}
                                    @error("item_{$question->item_number}")
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <div class="mt-8 flex flex-col items-center">
                            <button type="submit"
                                    id="submit-button"
                                    onclick="return confirmSubmit()"
                                    disabled
                                    class="inline-flex items-center px-8 py-4 bg-green-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span id="submit-button-text">Selesai & Submit Jawaban</span>
                            </button>

                            <p id="submit-hint" class="mt-3 text-sm text-red-500 font-medium">
                                Jawab semua {{ $questions->count() }} item untuk mengaktifkan tombol submit
                            </p>
                        </div>
                    </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        input[type="radio"] {
            -webkit-user-select: auto;
            -moz-user-select: auto;
            user-select: auto;
        }
        
        .timer-warning {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Highlight selected option */
        input[type="radio"]:checked + div {
            font-weight: 600;
        }

        .option-label:has(input:checked) {
            background-color: #EFF6FF;
            border-color: #3B82F6;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
        }
    </style>

    {{-- JavaScript --}}
    <script>
        // ====================================
        // NAVIGATION & SCROLL
        // ====================================
        function scrollToItem(itemNumber) {
            const item = document.querySelector(`div[data-item="${itemNumber}"]`);
            if (item) {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Highlight briefly
                item.classList.add('ring-4', 'ring-blue-500');
                setTimeout(() => {
                    item.classList.remove('ring-4', 'ring-blue-500');
                }, 1500);
            }
        }

        function updateNavigatorStatus() {
            const totalItems = {{ $questions->count() }};

            for (let i = 1; i <= totalItems; i++) {
                const radioButtons = document.querySelectorAll(`input[name="item_${i}"]`);
                const isAnswered = Array.from(radioButtons).some(radio => radio.checked);
                const navButton = document.querySelector(`button[data-item-nav="${i}"]`);

                if (navButton) {
                    if (isAnswered) {
                        navButton.className = 'aspect-square flex items-center justify-center rounded-lg text-sm font-bold transition-all duration-200 w-full border-2 shadow-sm hover:shadow-md hover:scale-105 bg-green-100 text-green-700 border-green-300 hover:bg-green-200';
                    } else {
                        navButton.className = 'aspect-square flex items-center justify-center rounded-lg text-sm font-bold transition-all duration-200 w-full border-2 shadow-sm hover:shadow-md hover:scale-105 bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:border-gray-400';
                    }
                }
            }
        }

        // ====================================
        // PROGRESS TRACKER
        // ====================================
        function updateProgress() {
            const totalItems = {{ $questions->count() }};
            let answeredCount = 0;

            for (let i = 1; i <= totalItems; i++) {
                const radioButtons = document.querySelectorAll(`input[name="item_${i}"]`);
                const isAnswered = Array.from(radioButtons).some(radio => radio.checked);
                if (isAnswered) {
                    answeredCount++;
                }
            }

            const percentage = Math.round((answeredCount / totalItems) * 100);
            document.getElementById('progress-bar').style.width = percentage + '%';
            document.getElementById('progress-percentage').textContent = percentage + '%';

            // Enable/disable submit button based on completion
            const submitButton = document.getElementById('submit-button');
            const submitHint = document.getElementById('submit-hint');
            const remaining = totalItems - answeredCount;

            if (answeredCount === totalItems) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed', 'disabled:transform-none', 'disabled:shadow-none');
                if (submitHint) {
                    submitHint.textContent = 'Semua item sudah dijawab. Klik untuk submit!';
                    submitHint.classList.remove('text-red-500');
                    submitHint.classList.add('text-green-600');
                }
            } else {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                if (submitHint) {
                    submitHint.textContent = `Masih ada ${remaining} item yang belum dijawab`;
                    submitHint.classList.remove('text-green-600');
                    submitHint.classList.add('text-red-500');
                }
            }

            // Update navigator status
            updateNavigatorStatus();
        }

        function confirmSubmit() {
            const totalItems = {{ $questions->count() }};
            let unansweredItems = [];

            for (let i = 1; i <= totalItems; i++) {
                const radioButtons = document.querySelectorAll(`input[name="item_${i}"]`);
                const isAnswered = Array.from(radioButtons).some(radio => radio.checked);
                if (!isAnswered) {
                    unansweredItems.push(i);
                }
            }

            if (unansweredItems.length > 0) {
                alert(`⚠️ Masih ada ${unansweredItems.length} item yang belum dijawab!\n\nItem yang belum dijawab: ${unansweredItems.slice(0, 10).join(', ')}${unansweredItems.length > 10 ? '...' : ''}`);
                
                // Scroll to first unanswered
                const firstUnanswered = document.querySelector(`div[data-item="${unansweredItems[0]}"]`);
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstUnanswered.classList.add('ring-4', 'ring-red-500');
                    setTimeout(() => {
                        firstUnanswered.classList.remove('ring-4', 'ring-red-500');
                    }, 2000);
                }
                
                return false;
            }

            return confirm('Apakah Anda yakin ingin menyelesaikan tes PAPI Kostick?\n\nPastikan semua jawaban sudah benar karena Anda tidak dapat mengubahnya lagi.');
        }

        // ====================================
        // TIMER FUNCTIONALITY
        // ====================================
        function timer(seconds) {
            return {
                timeLeft: seconds,
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;
                        
                        if (this.timeLeft === 300) {
                            const timerDisplay = document.getElementById('timer-display');
                            if (timerDisplay) {
                                timerDisplay.classList.add('timer-warning');
                                timerDisplay.style.color = '#dc3545';
                            }
                            alert('⚠️ Perhatian! Waktu tersisa 5 menit!');
                        }
                        
                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            alert('⏰ Waktu habis! Test akan otomatis di-submit.');
                            document.getElementById('papi-form').submit();
                        }
                    }, 1000);
                },
                formatTime() {
                    const hours = Math.floor(this.timeLeft / 3600);
                    const minutes = Math.floor((this.timeLeft % 3600) / 60);
                    const seconds = this.timeLeft % 60;

                    if (hours > 0) {
                        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    } else {
                        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                }
            }
        }

        // ====================================
        // ANTI-CHEATING (Simplified for PAPI)
        // ====================================
        const ANTI_CHEAT_CONFIG = {
            MAX_VIOLATIONS: 3,
            TEST_ID: {{ $test->id }},
            CSRF_TOKEN: '{{ csrf_token() }}'
        };

        let violationCount = 0;

        function updateViolationDisplay() {
            document.getElementById('violation-count').textContent = violationCount;
            document.getElementById('hidden-violation-count').value = violationCount;
        }

        function showViolationWarning(message) {
            violationCount++;
            updateViolationDisplay();
            
            alert(`⚠️ PELANGGARAN #${violationCount}\n\n${message}\n\nPelanggaran: ${violationCount} dari ${ANTI_CHEAT_CONFIG.MAX_VIOLATIONS}`);
            
            if (violationCount >= ANTI_CHEAT_CONFIG.MAX_VIOLATIONS) {
                alert('❌ Anda telah melakukan 3 pelanggaran. Test akan dihentikan!');
                window.location.href = '/test/terminated';
            }
        }

        // Prevent screenshot
        document.addEventListener('keyup', function(e) {
            if (e.keyCode === 44 || e.key === 'PrintScreen') {
                showViolationWarning('Screenshot tidak diperbolehkan!');
            }
        });

        // Prevent right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showViolationWarning('Klik kanan tidak diperbolehkan!');
        });

        // Prevent copy
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            showViolationWarning('Menyalin teks tidak diperbolehkan!');
        });

        // Track tab switches
        let tabSwitchCount = 0;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                tabSwitchCount++;
                document.getElementById('hidden-tab-switches').value = tabSwitchCount;
                
                if (tabSwitchCount >= 3) {
                    alert('⚠️ Anda telah meninggalkan halaman 3 kali. Test akan otomatis diselesaikan!');
                    document.getElementById('papi-form').submit();
                }
            }
        });

        // Initialize
        window.addEventListener('load', function() {
            updateProgress();
            updateNavigatorStatus();
            console.log('✅ PAPI Test system initialized');
        });
    </script>
</x-app-layout>