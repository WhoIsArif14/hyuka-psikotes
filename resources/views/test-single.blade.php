<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
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
                            <div class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow">
                                Sisa Waktu: <span x-text="formatTime()"></span>
                            </div>
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
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            {{-- Nomor dan Pertanyaan --}}
                            <div class="font-semibold text-lg mb-4">
                                <p class="text-gray-700">{{ $currentNumber }}. {{ $question->question_text }}</p>
                                @if ($question->image_path)
                                    <img src="{{ asset('storage/' . $question->image_path) }}" 
                                         alt="Gambar Soal"
                                         class="mt-4 rounded-md max-w-full md:max-w-lg">
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
                                                         class="mt-3 rounded-md max-w-xs">
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
                            <p>Anda dapat kembali ke soal sebelumnya untuk mengubah jawaban</p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // --- CEGAH PINDAH TAB ---
        let hasLeftPage = false;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !hasLeftPage) {
                hasLeftPage = true;
                alert('Anda telah meninggalkan halaman tes. Tes akan otomatis diselesaikan.');
                document.getElementById('test-form').submit();
            }
        });

        // --- TIMER ---
        function timer(seconds) {
            return {
                timeLeft: seconds,
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            if (!hasLeftPage) {
                                alert('Waktu habis! Tes akan otomatis diselesaikan.');
                                // Submit dengan action=submit
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

        // Auto-save on selection (optional)
        document.querySelectorAll('input[name="answer"]').forEach(input => {
            input.addEventListener('change', function() {
                // Visual feedback that answer is saved
                console.log('Jawaban tersimpan');
            });
        });
    </script>
</x-app-layout>