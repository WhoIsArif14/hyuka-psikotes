<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- INFORMASI TES & TIMER --}}
                    <div
                        x-data="timer({{ $test->duration_minutes * 60 }})"
                        x-init="startTimer()"
                        class="mb-8 p-4 border-l-4 border-blue-400 bg-blue-50"
                    >
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $test->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $test->description }}</p>
                            </div>
                            <div class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow">
                                Sisa Waktu: <span x-text="formatTime()"></span>
                            </div>
                        </div>
                    </div>

                    {{-- FORM SOAL --}}
                    <form id="test-form" method="POST" action="{{ route('tests.store', $test) }}">
                        @csrf
                        <div class="space-y-8">
                            @foreach ($test->questions as $question)
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                    <p class="font-semibold text-lg mb-4">{{ $loop->iteration }}. {{ $question->question_text }}</p>

                                    <div class="space-y-3">
                                        @foreach($question->options as $option)
                                            <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-100">
                                                <input type="radio" name="questions[{{ $question->id }}]" value="{{ $option->id }}" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Selesai Mengerjakan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT UNTUK TIMER --}}
    <script>
        function timer(seconds) {
            return {
                timeLeft: seconds,
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;

                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            // Otomatis submit form jika waktu habis
                            document.getElementById('test-form').submit();
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
    </script>
</x-app-layout>