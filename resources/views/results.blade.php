<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Tes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 md:p-12 text-gray-900 text-center">

                    <h3 class="text-lg font-medium text-gray-600">
                        Anda telah menyelesaikan tes:
                    </h3>
                    <h1 class="mt-2 mb-6 text-4xl font-extrabold text-gray-800">
                        {{ $testResult->test->title }}
                    </h1>

                    <div class="inline-block bg-gray-100 rounded-lg p-6">
                        <p class="text-base font-medium text-gray-500">SKOR AKHIR ANDA</p>
                        <p class="text-7xl font-bold text-indigo-600 my-2">
                            {{ $testResult->score }}
                        </p>
                    </div>

                    {{-- Menampilkan Interpretasi --}}
                    @if ($interpretation)
                        <div class="mt-8 text-left bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r-lg">
                            <h4 class="font-bold text-indigo-800">Interpretasi Hasil:</h4>
                            <p class="mt-1 text-indigo-700">
                                {{ $interpretation->interpretation_text }}
                            </p>
                        </div>
                    @endif

                    {{-- Review Jawaban --}}
                    <div class="mt-10 text-left border-t pt-8">
                        <h3 class="text-2xl font-bold mb-6 text-gray-800">Review Jawaban Anda</h3>
                        <div class="space-y-6">
                            @foreach ($testResult->test->questions as $question)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="font-semibold text-gray-900">{{ $loop->iteration }}.
                                        {{ $question->question_text }}</p>
                                    <div class="mt-3 space-y-2">
                                        @foreach ($question->options as $option)
                                            @php
                                                // Cek apakah ini jawaban yang dipilih pengguna
                                                $userAnswer = $testResult->userAnswers->firstWhere(
                                                    'question_id',
                                                    $question->id,
                                                );
                                                $isUserChoice = $userAnswer && $userAnswer->option_id == $option->id;

                                                // Tentukan style berdasarkan jawaban
                                                $style = '';
                                                if ($isUserChoice && $option->point > 0) {
                                                    // Jawaban benar (poin > 0) dan dipilih
                                                    $style = 'bg-green-100 border-green-400';
                                                } elseif ($isUserChoice && $option->point == 0) {
                                                    // Jawaban salah (poin = 0) dan dipilih
                                                    $style = 'bg-red-100 border-red-400';
                                                } elseif ($option->point > 0) {
                                                    // Jawaban benar tapi tidak dipilih
                                                    $style = 'border-gray-300';
                                                }
                                            @endphp
                                            <div class="flex items-center p-3 border rounded-md {{ $style }}">
                                                @if ($isUserChoice)
                                                    <span class="text-indigo-600 font-bold mr-2">Pilihan Anda:</span>
                                                @endif
                                                <span class="flex-1">{{ $option->option_text }}</span>
                                                @if ($option->point > 0)
                                                    <span class="ml-4 font-bold text-green-700">(Benar)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-10">
                        <p class="text-gray-600">Terima kasih telah berpartisipasi. Anda akan segera keluar dari sistem.
                        </p>

                        {{-- PERBAIKAN ADA DI BARIS DI BAWAH INI --}}
                        <a href="{{ route('custom.logout') }}"
                            class="mt-4 inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Selesai & Keluar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
