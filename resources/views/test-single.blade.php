<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12" data-user-id="{{ auth()->user()->email }}" data-test-id="{{ $test->id }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- INFORMASI TES & TIMER --}}
                    <div x-data="timer({{ $timeRemaining }})" x-init="startTimer()"
                        class="mb-8 p-4 border-l-4 border-blue-400 bg-blue-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $test->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Soal {{ $currentNumber }} dari {{ $totalQuestions }}
                                </p>
                            </div>
                            <div id="timer-display" class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow">
                                Sisa Waktu: <span x-text="formatTime()"></span>
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
                            <span>{{ round(($currentNumber / $totalQuestions) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ ($currentNumber / $totalQuestions) * 100 }}%"></div>
                        </div>
                    </div>

                    {{-- FORM SOAL --}}
                    <form id="test-form" method="POST" 
                          action="{{ route('tests.answer', ['test' => $test->id, 'number' => $currentNumber]) }}">
                        @csrf
                        
                        <input type="hidden" name="violation_count" id="hidden-violation-count" value="0">
                        <input type="hidden" name="tab_switches" id="hidden-tab-switches" value="0">
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            {{-- Nomor dan Pertanyaan --}}
                            <div class="font-semibold text-lg mb-4">
                                <p class="text-gray-700">{{ $currentNumber }}. {{ $question->question_text }}</p>
                                @if ($question->image_path)
                                    <img src="{{ asset('storage/' . $question->image_path) }}" 
                                         alt="Gambar Soal"
                                         class="mt-4 rounded-md max-w-full md:max-w-lg select-none pointer-events-none">
                                @endif
                            </div>

                            {{-- Opsi Jawaban --}}
                            @php
                                $options = is_string($question->options)
                                    ? json_decode($question->options, true)
                                    : $question->options ?? [];
                            @endphp

                            @if (is_array($options) && count($options) > 0)
                                <div class="space-y-3">
                                    @foreach ($options as $index => $option)
                                        <label
                                            class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors
                                                   {{ $savedAnswer == $index ? 'bg-blue-50 border-blue-400' : 'border-gray-200' }}">
                                            <input type="radio" 
                                                   name="answer" 
                                                   value="{{ $index }}"
                                                   {{ $savedAnswer == $index ? 'checked' : '' }}
                                                   class="h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500 mt-0.5">

                                            <div class="ml-3 text-gray-700 flex-1">
                                                <span class="font-medium">{{ chr(65 + $index) }}.</span>
                                                <span class="ml-2">{{ $option['text'] ?? '' }}</span>

                                                @if (!empty($option['image_path']))
                                                    <img src="{{ asset('storage/' . $option['image_path']) }}"
                                                         alt="Gambar Opsi" 
                                                         class="mt-3 rounded-md max-w-xs select-none pointer-events-none">
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-red-500 text-sm">Tidak ada opsi jawaban untuk soal ini.</p>
                            @endif
                        </div>

                        {{-- NAVIGATION BUTTONS --}}
                        <div class="flex justify-between items-center">
                            {{-- Previous Button --}}
                            @if ($currentNumber > 1)
                                <button type="submit" 
                                        name="action" 
                                        value="previous"
                                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                                    ← Sebelumnya
                                </button>
                            @else
                                <div></div>
                            @endif

                            {{-- Next or Submit Button --}}
                            <div class="flex gap-3">
                                @if ($currentNumber < $totalQuestions)
                                    <button type="submit" 
                                            name="action" 
                                            value="next"
                                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                        Selanjutnya →
                                    </button>
                                @else
                                    <button type="submit" 
                                            name="action" 
                                            value="submit"
                                            onclick="return confirm('Apakah Anda yakin ingin menyelesaikan tes? Pastikan semua jawaban sudah benar.')"
                                            class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                        ✓ Selesai Mengerjakan
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Navigation Info --}}
                        <div class="mt-4 text-center text-sm text-gray-500">
                            <p>⚠️ Peringatan: Jangan keluar dari halaman ini, screenshot, atau buka developer tools</p>
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
            user-select: auto;
        }
        
        /* Prevent image dragging */
        img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
        
        /* Timer warning animation */
        .timer-warning {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    {{-- ========================================
         ANTI-CHEATING SYSTEM
         ======================================== --}}
    <script>
        // ====================================
        // CONFIGURATION
        // ====================================
        const ANTI_CHEAT_CONFIG = {
            MAX_VIOLATIONS: 3,
            MAX_TAB_SWITCHES: 3,
            TEST_ID: {{ $test->id }},
            CSRF_TOKEN: '{{ csrf_token() }}'
        };

        let violationCount = 0;
        let tabSwitchCount = 0;
        let hasLeftPage = false;

        // ====================================
        // UTILITY FUNCTIONS
        // ====================================
        
        function updateViolationDisplay() {
            document.getElementById('violation-count').textContent = violationCount;
            document.getElementById('hidden-violation-count').value = violationCount;
            document.getElementById('hidden-tab-switches').value = tabSwitchCount;
        }

        function logViolation(type, details = '') {
            fetch('/api/log-violation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': ANTI_CHEAT_CONFIG.CSRF_TOKEN
                },
                body: JSON.stringify({
                    test_id: ANTI_CHEAT_CONFIG.TEST_ID,
                    type: type,
                    details: details,
                    timestamp: new Date().toISOString()
                })
            }).catch(err => console.error('Logging error:', err));
        }

        function showViolationWarning(message) {
            violationCount++;
            updateViolationDisplay();
            logViolation(message.type || 'unknown', message.details || '');
            
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.95);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s;
            `;

            overlay.innerHTML = `
                <div style="
                    background: white;
                    padding: 40px;
                    border-radius: 15px;
                    text-align: center;
                    max-width: 500px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
                    animation: slideIn 0.3s;
                ">
                    <div style="font-size: 60px; margin-bottom: 20px;">⚠️</div>
                    <h2 style="color: #dc3545; margin-bottom: 20px; font-size: 24px; font-weight: bold;">
                        PELANGGARAN TERDETEKSI!
                    </h2>
                    <p style="font-size: 18px; margin-bottom: 15px; color: #333; line-height: 1.6;">
                        ${message.text}
                    </p>
                    <p style="font-size: 16px; color: #666; margin-bottom: 25px;">
                        Pelanggaran ke-<span style="color: #dc3545; font-weight: bold; font-size: 24px;">${violationCount}</span> 
                        dari ${ANTI_CHEAT_CONFIG.MAX_VIOLATIONS}
                    </p>
                    ${violationCount >= ANTI_CHEAT_CONFIG.MAX_VIOLATIONS ? 
                        '<p style="color: #dc3545; font-weight: bold; font-size: 18px; margin-bottom: 20px;">⏰ Test akan dihentikan dalam 3 detik!</p>' :
                        '<button onclick="this.parentElement.parentElement.remove()" style="background: #007bff; color: white; border: none; padding: 15px 40px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,123,255,0.3); transition: all 0.3s;">Saya Mengerti</button>'
                    }
                </div>
            `;

            document.body.appendChild(overlay);

            // Auto-redirect jika mencapai batas
            if (violationCount >= ANTI_CHEAT_CONFIG.MAX_VIOLATIONS) {
                setTimeout(() => {
                    window.location.href = '/test/terminated';
                }, 3000);
            }
        }

        // ====================================
        // 1. PRINT SCREEN DETECTION
        // ====================================
        document.addEventListener('keyup', function(e) {
            if (e.keyCode === 44 || e.key === 'PrintScreen') {
                e.preventDefault();
                showViolationWarning({
                    type: 'screenshot_attempt',
                    text: '🚫 Screenshot TIDAK DIPERBOLEHKAN!<br><br>Mengambil screenshot dapat mengakibatkan test dihentikan.',
                    details: 'Print Screen key pressed'
                });
                
                // Blur effect
                document.body.style.filter = 'blur(20px)';
                setTimeout(() => {
                    document.body.style.filter = 'none';
                }, 2000);
            }
        });

        // Windows/Mac Screenshot Tools
        document.addEventListener('keydown', function(e) {
            // Windows: Win + Shift + S
            if ((e.key === 's' || e.key === 'S') && e.shiftKey && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                showViolationWarning({
                    type: 'screenshot_attempt',
                    text: '🚫 Screenshot Tool Windows Terdeteksi!<br><br>Penggunaan alat screenshot tidak diperbolehkan.',
                    details: 'Windows screenshot tool (Win+Shift+S)'
                });
            }
            
            // Mac: Cmd + Shift + 3/4/5
            if ((e.key === '3' || e.key === '4' || e.key === '5') && e.shiftKey && e.metaKey) {
                e.preventDefault();
                showViolationWarning({
                    type: 'screenshot_attempt',
                    text: '🚫 Screenshot Mac Terdeteksi!<br><br>Penggunaan screenshot Mac tidak diperbolehkan.',
                    details: 'Mac screenshot (Cmd+Shift+' + e.key + ')'
                });
            }
        });

        // ====================================
        // 2. TAB SWITCH DETECTION
        // ====================================
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !hasLeftPage) {
                tabSwitchCount++;
                
                if (tabSwitchCount >= ANTI_CHEAT_CONFIG.MAX_TAB_SWITCHES) {
                    hasLeftPage = true;
                    alert('⚠️ Anda telah meninggalkan halaman test ' + tabSwitchCount + ' kali. Test akan otomatis diselesaikan!');
                    document.getElementById('test-form').submit();
                } else {
                    setTimeout(() => {
                        if (!document.hidden) {
                            showViolationWarning({
                                type: 'tab_switch',
                                text: `⚠️ Anda Meninggalkan Halaman Test!<br><br>Peringatan ${tabSwitchCount} dari ${ANTI_CHEAT_CONFIG.MAX_TAB_SWITCHES}.<br>Tetap fokus pada halaman ini!`,
                                details: `Tab switch #${tabSwitchCount}`
                            });
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
        // 3. RIGHT CLICK PREVENTION
        // ====================================
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showViolationWarning({
                type: 'right_click',
                text: '🚫 Klik Kanan Tidak Diperbolehkan!<br><br>Silakan gunakan navigasi yang tersedia.',
                details: 'Right click attempted'
            });
        });

        // ====================================
        // 4. DEVELOPER TOOLS PREVENTION
        // ====================================
        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                showViolationWarning({
                    type: 'devtools_attempt',
                    text: '🚫 Developer Tools Tidak Diperbolehkan!<br><br>Akses ke developer tools akan dianggap sebagai pelanggaran.',
                    details: 'DevTools hotkey pressed'
                });
            }
        });

        // ====================================
        // 5. COPY PREVENTION
        // ====================================
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            showViolationWarning({
                type: 'copy_attempt',
                text: '🚫 Menyalin Teks Tidak Diperbolehkan!<br><br>Semua konten test tidak boleh disalin.',
                details: 'Copy text attempted'
                });
        });

        // ====================================
        // 6. CONSOLE WARNING
        // ====================================
        console.log('%c🚫 STOP!', 'color: red; font-size: 50px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);');
        console.log('%cJANGAN GUNAKAN CONSOLE INI!', 'color: red; font-size: 30px; font-weight: bold;');
        console.log('%cMenggunakan developer tools akan dianggap sebagai PELANGGARAN dan test Anda akan dihentikan.', 'color: red; font-size: 16px;');

        // ====================================
        // 7. TIMER FUNCTIONALITY (Original + Enhanced)
        // ====================================
        function timer(seconds) {
            return {
                timeLeft: seconds,
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;
                        
                        // Warning saat 5 menit tersisa
                        if (this.timeLeft === 300) {
                            const timerDisplay = document.getElementById('timer-display');
                            if (timerDisplay) {
                                timerDisplay.classList.add('timer-warning');
                                timerDisplay.style.color = '#dc3545';
                            }
                            alert('⚠️ Perhatian! Waktu tersisa 5 menit!');
                        }
                        
                        // Auto submit saat waktu habis
                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            if (!hasLeftPage) {
                                hasLeftPage = true;
                                alert('⏰ Waktu habis! Test akan otomatis di-submit.');
                                const form = document.getElementById('test-form');
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'action';
                                input.value = 'submit';
                                form.appendChild(input);
                                form.submit();
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
        // 8. HEARTBEAT SYSTEM
        // ====================================
        setInterval(function() {
            fetch('/api/test-heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': ANTI_CHEAT_CONFIG.CSRF_TOKEN
                },
                body: JSON.stringify({
                    test_id: ANTI_CHEAT_CONFIG.TEST_ID,
                    timestamp: new Date().toISOString(),
                    violations: violationCount,
                    tab_switches: tabSwitchCount
                })
            }).catch(err => console.error('Heartbeat error:', err));
        }, 30000); // Every 30 seconds

        // ====================================
        // 9. INITIALIZATION
        // ====================================
        window.addEventListener('load', function() {
            console.log('✅ Anti-cheating system initialized');
            
            // Visual indicator for users
            const indicator = document.createElement('div');
            indicator.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#28a745;color:white;padding:8px 16px;border-radius:20px;font-size:12px;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.2);z-index:9999;';
            indicator.textContent = '🔒 Secure Test Mode';
            document.body.appendChild(indicator);
            
            // Remove indicator after 5 seconds
            setTimeout(() => {
                indicator.style.opacity = '0';
                indicator.style.transition = 'opacity 1s';
                setTimeout(() => indicator.remove(), 1000);
            }, 5000);
        });

        // Auto-save on selection (optional enhancement)
        document.querySelectorAll('input[name="answer"]').forEach(input => {
            input.addEventListener('change', function() {
                console.log('Answer selected:', this.value);
            });
        });
    </script>

    {{-- CSS Animations --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { 
                transform: scale(0.8);
                opacity: 0;
            }
            to { 
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</x-app-layout>