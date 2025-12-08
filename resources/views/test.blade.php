<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12" data-user-id="{{ auth()->user()->email }}" data-test-id="{{ $test->id }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- INFORMASI TES & TIMER --}}
                    <div x-data="timer({{ $test->duration_minutes * 60 }})" x-init="startTimer()"
                        class="mb-8 p-4 border-l-4 border-blue-400 bg-blue-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $test->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $test->description }}</p>
                            </div>
                            <div id="timer-display" class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow">
                                Sisa Waktu: <span x-text="formatTime()"></span>
                            </div>
                        </div>
                        
                        {{-- Warning untuk pelanggaran --}}
                        <div x-show="violationWarning" x-cloak 
                            class="mt-4 p-3 bg-red-100 border border-red-400 rounded-lg text-red-700 text-sm">
                            ⚠️ <span x-text="violationMessage"></span>
                        </div>
                    </div>

                    {{-- Progress Tracker --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                Progress: <span id="answered-count">0</span> dari {{ $questions->count() }} soal terjawab
                            </span>
                            <span class="text-sm font-medium text-gray-700">
                                Pelanggaran: <span id="violation-count" class="text-red-600 font-bold">0</span> / 3
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    {{-- FORM SOAL --}}
                    <form id="test-form" method="POST" action="{{ route('tests.store', $test) }}">
                        @csrf
                        <input type="hidden" name="violation_count" id="hidden-violation-count" value="0">
                        <input type="hidden" name="tab_switches" id="hidden-tab-switches" value="0">
                        
                        <div class="space-y-8">
                            {{-- ✅ Cek sumber pertanyaan --}}
                            @php
                                $questions = isset($alatTes) ? $alatTes->questions : $test->questions;
                            @endphp

                            @foreach ($questions as $question)
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                    <div class="font-semibold text-lg mb-4">
                                        <p>{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                        @if ($question->image_path)
                                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Soal"
                                                class="mt-4 rounded-md max-w-full md:max-w-lg select-none pointer-events-none">
                                        @endif
                                    </div>

                                    @php
                                        // Pastikan opsi dalam format array
                                        $options = is_string($question->options)
                                            ? json_decode($question->options, true)
                                            : $question->options ?? [];
                                    @endphp

                                    @if (is_array($options) && count($options) > 0)
                                        <div class="space-y-3">
                                            @foreach ($options as $option)
                                                <label
                                                    class="flex items-start p-3 border rounded-md cursor-pointer hover:bg-gray-100 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                                    <input type="radio" 
                                                        name="questions[{{ $question->id }}]"
                                                        value="{{ $option['index'] ?? $loop->index }}"
                                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 mt-1"
                                                        onchange="updateProgress()">

                                                    <div class="ml-3 text-gray-700">
                                                        <span>{{ $option['text'] ?? '' }}</span>

                                                        @if (!empty($option['image_path']))
                                                            <img src="{{ asset('storage/' . $option['image_path']) }}"
                                                                alt="Gambar Opsi" class="mt-2 rounded-md max-w-xs select-none pointer-events-none">
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-red-500 text-sm">Tidak ada opsi jawaban untuk soal ini.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <p>⚠️ Peringatan:</p>
                                <ul class="list-disc list-inside text-xs mt-1">
                                    <li>Jangan keluar dari halaman ini</li>
                                    <li>Jangan mencoba screenshot</li>
                                    <li>Jangan buka developer tools</li>
                                    <li>3x pelanggaran = test otomatis dihentikan</li>
                                </ul>
                            </div>
                            
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Selesai Mengerjakan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Styles untuk anti-select dan anti-copy --}}
    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Allow selection hanya untuk radio button */
        input[type="radio"] {
            -webkit-user-select: auto;
            -moz-user-select: auto;
            -ms-user-select: auto;
            user-select: auto;
        }
        
        /* Timer warning animation */
        .timer-warning {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Prevent image dragging */
        img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
    </style>

    {{-- LOAD ANTI-CHEATING SCRIPT --}}
    <script src="{{ asset('js/anti-cheating.js') }}"></script>

    <script>
        // ====================================
        // ENHANCED SECURITY FEATURES
        // ====================================
        
        let violationCount = 0;
        let tabSwitchCount = 0;
        let hasLeftPage = false;
        const MAX_VIOLATIONS = 3;
        const totalQuestions = {{ $questions->count() }};

        // Update progress bar
        function updateProgress() {
            const radios = document.querySelectorAll('input[type="radio"]:checked');
            const answeredCount = new Set([...radios].map(r => r.name)).size;
            
            document.getElementById('answered-count').textContent = answeredCount;
            
            const progressPercent = (answeredCount / totalQuestions) * 100;
            document.getElementById('progress-bar').style.width = progressPercent + '%';
        }

        // Update violation count display
        function updateViolationDisplay() {
            document.getElementById('violation-count').textContent = violationCount;
            document.getElementById('hidden-violation-count').value = violationCount;
            document.getElementById('hidden-tab-switches').value = tabSwitchCount;
        }

        // Log violation to server
        function logViolation(type, details = '') {
            fetch('/api/log-violation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    test_id: {{ $test->id }},
                    type: type,
                    details: details,
                    timestamp: new Date().toISOString()
                })
            }).catch(err => console.error('Error logging violation:', err));
        }

        // Show violation warning
        function showViolationWarning(message) {
            violationCount++;
            updateViolationDisplay();
            
            // Show Alpine.js warning (jika ada)
            if (window.Alpine) {
                // Trigger Alpine data update jika ada
            }
            
            // Custom alert
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
            `;

            overlay.innerHTML = `
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    text-align: center;
                    max-width: 500px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                ">
                    <h2 style="color: #dc3545; margin-bottom: 20px; font-size: 24px; font-weight: bold;">
                        ⚠️ PELANGGARAN TERDETEKSI
                    </h2>
                    <p style="font-size: 16px; margin-bottom: 15px; color: #333;">
                        ${message}
                    </p>
                    <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                        Pelanggaran ke-${violationCount} dari ${MAX_VIOLATIONS}
                    </p>
                    ${violationCount >= MAX_VIOLATIONS ? 
                        '<p style="color: #dc3545; font-weight: bold; font-size: 16px;">Test akan dihentikan dalam 3 detik!</p>' :
                        '<button id="closeWarning" style="background: #007bff; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold;">Saya Mengerti</button>'
                    }
                </div>
            `;

            document.body.appendChild(overlay);

            // Jika mencapai batas pelanggaran
            if (violationCount >= MAX_VIOLATIONS) {
                setTimeout(() => {
                    alert('Test dihentikan karena terlalu banyak pelanggaran!');
                    document.getElementById('test-form').submit();
                }, 3000);
            } else {
                document.getElementById('closeWarning')?.addEventListener('click', () => {
                    overlay.remove();
                });
            }
        }

        // ====================================
        // PRINT SCREEN DETECTION
        // ====================================
        document.addEventListener('keyup', function(e) {
            if (e.keyCode === 44 || e.key === 'PrintScreen') {
                e.preventDefault();
                logViolation('screenshot_attempt', 'Print Screen key detected');
                showViolationWarning('Terdeteksi percobaan screenshot! Screenshot tidak diperbolehkan selama ujian.');
                
                // Blur screen
                document.body.style.filter = 'blur(10px)';
                setTimeout(() => {
                    document.body.style.filter = 'none';
                }, 2000);
            }
        });

        // Windows screenshot tool (Win + Shift + S)
        document.addEventListener('keydown', function(e) {
            if ((e.key === 's' || e.key === 'S') && e.shiftKey && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                logViolation('screenshot_attempt', 'Windows screenshot tool detected');
                showViolationWarning('Screenshot tool Windows terdeteksi! Tidak diperbolehkan menggunakan screenshot.');
            }
            
            // Mac screenshot (Cmd + Shift + 3/4/5)
            if ((e.key === '3' || e.key === '4' || e.key === '5') && e.shiftKey && e.metaKey) {
                e.preventDefault();
                logViolation('screenshot_attempt', 'Mac screenshot detected');
                showViolationWarning('Screenshot Mac terdeteksi! Tidak diperbolehkan menggunakan screenshot.');
            }
        });

        // ====================================
        // TAB SWITCH DETECTION
        // ====================================
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !hasLeftPage) {
                tabSwitchCount++;
                logViolation('tab_switch', `Tab switch #${tabSwitchCount}`);
                
                if (tabSwitchCount >= 3) {
                    hasLeftPage = true;
                    alert('Anda telah meninggalkan halaman tes 3 kali. Tes akan otomatis diselesaikan.');
                    document.getElementById('test-form').submit();
                } else {
                    // Show warning when return
                    setTimeout(() => {
                        if (!document.hidden) {
                            showViolationWarning(`Anda telah meninggalkan halaman tes (${tabSwitchCount}x). Tetap fokus pada halaman ini!`);
                        }
                    }, 500);
                }
            }
        });

        window.addEventListener('blur', function() {
            if (!hasLeftPage) {
                logViolation('window_blur', 'Window lost focus');
            }
        });

        // ====================================
        // RIGHT CLICK PREVENTION
        // ====================================
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            logViolation('right_click', 'Right click attempted');
            showViolationWarning('Klik kanan tidak diperbolehkan selama ujian!');
        });

        // ====================================
        // DEVELOPER TOOLS PREVENTION
        // ====================================
        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                logViolation('devtools_attempt', 'Developer tools access attempted');
                showViolationWarning('Developer tools tidak diperbolehkan!');
            }
        });

        // ====================================
        // COPY PREVENTION
        // ====================================
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            logViolation('copy_attempt', 'Copy text attempted');
            showViolationWarning('Menyalin teks tidak diperbolehkan!');
        });

        // ====================================
        // TIMER FUNCTIONALITY
        // ====================================
        function timer(seconds) {
            return {
                timeLeft: seconds,
                violationWarning: false,
                violationMessage: '',
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;
                        
                        // Warning saat 5 menit tersisa
                        if (this.timeLeft === 300) {
                            const timerDisplay = document.getElementById('timer-display');
                            if (timerDisplay) {
                                timerDisplay.classList.add('timer-warning', 'text-red-600');
                            }
                            alert('⚠️ Perhatian! Waktu tersisa 5 menit!');
                        }
                        
                        // Auto submit saat waktu habis
                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            if (!hasLeftPage) {
                                alert('⏰ Waktu habis! Test akan otomatis di-submit.');
                                document.getElementById('test-form').submit();
                            }
                        }
                    }, 1000);
                },
                formatTime() {
                    const minutes = Math.floor(this.timeLeft / 60);
                    const seconds = this.timeLeft % 60;
                    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                }
            }
        }

        // ====================================
        // FORM SUBMIT VALIDATION
        // ====================================
        document.getElementById('test-form').addEventListener('submit', function(e) {
            const radios = document.querySelectorAll('input[type="radio"]:checked');
            const answeredCount = new Set([...radios].map(r => r.name)).size;
            
            if (answeredCount < totalQuestions) {
                if (!confirm(`Anda baru menjawab ${answeredCount} dari ${totalQuestions} soal. Yakin ingin submit?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            
            return true;
        });

        // ====================================
        // HEARTBEAT - Check user still active
        // ====================================
        setInterval(function() {
            fetch('/api/test-heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    test_id: {{ $test->id }},
                    timestamp: new Date().toISOString(),
                    violations: violationCount,
                    tab_switches: tabSwitchCount
                })
            }).catch(err => console.error('Heartbeat error:', err));
        }, 30000); // Every 30 seconds

        // ====================================
        // CONSOLE WARNING
        // ====================================
        console.log('%cJANGAN GUNAKAN CONSOLE INI!', 'color: red; font-size: 30px; font-weight: bold;');
        console.log('%cMenggunakan developer tools akan dianggap sebagai pelanggaran.', 'color: red; font-size: 16px;');

        // ====================================
        // INITIALIZATION
        // ====================================
        window.addEventListener('load', function() {
            console.log('Anti-cheating system initialized');
            updateProgress(); // Initial progress update
        });
    </script>
</x-app-layout>