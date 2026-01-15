<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Petunjuk Pengerjaan - {{ $alatTes->name }}
            </h2>
            <a href="{{ route('tests.dashboard', $test->id) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $exampleCount = is_array($exampleQuestions) ? count($exampleQuestions) : 0;
        $hasExample1 = isset($exampleQuestions[0]);
        $hasExample2 = isset($exampleQuestions[1]);
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Tabs Navigation - Dynamic based on available examples -->
                <div class="grid grid-cols-{{ $exampleCount > 0 ? ($exampleCount + 2) : 2 }} border-b">
                    <button onclick="showTab('tentang')" id="tab-tentang"
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-indigo-600 text-gray-900 bg-gray-50">
                        Tentang Soal
                    </button>
                    @if($hasExample1)
                    <button onclick="showTab('contoh1')" id="tab-contoh1"
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        Contoh 1
                    </button>
                    @endif
                    @if($hasExample2)
                    <button onclick="showTab('contoh2')" id="tab-contoh2"
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        Contoh 2
                    </button>
                    @endif
                    <button onclick="showTab('kesiapan')" id="tab-kesiapan"
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        Kesiapan
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6 md:p-8">

                    <!-- TAB 1: Tentang Soal -->
                    <div id="content-tentang" class="tab-content">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tentang Soal
                            </h2>
                        </div>

                        <div class="prose max-w-none">
                            <div class="space-y-4 text-gray-700 leading-relaxed">
                                @if ($alatTes->instructions)
                                    {!! nl2br(e($alatTes->instructions)) !!}
                                @else
                                    <p>Soal terdiri atas kalimat-kalimat.</p>
                                    <p>Pada setiap kalimat satu kata hilang dan disediakan 5 (lima) kata pilihan sebagai
                                        penggantinya.</p>
                                    <p>Pilihlah kata yang tepat yang dapat menyempurnakan kalimat itu!</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <button onclick="showTab('contoh1')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Lihat Contoh Soal
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: Contoh 1 -->
                    @if($hasExample1)
                    <div id="content-contoh1" class="tab-content hidden">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Contoh Soal 1
                            </h2>
                        </div>

                        @if (isset($exampleQuestions[0]))
                            <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                <p class="text-gray-800 font-medium text-lg mb-6">
                                    {{ $exampleQuestions[0]['question'] ?? 'Contoh soal tidak tersedia' }}
                                </p>

                                @php $ex0 = $exampleQuestions[0]; @endphp
                                @if (($ex0['type'] ?? '') === 'HAFALAN')
                                    <div class="bg-purple-100 border border-purple-300 rounded p-3 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-bold text-purple-900">📚 HAFALKAN
                                                ({{ $ex0['duration_seconds'] ?? 10 }} detik)</p>
                                        </div>
                                        @if (($ex0['memory_type'] ?? 'TEXT') === 'IMAGE')
                                            @php
                                                $m = $ex0['memory_content'] ?? '';
                                                if (preg_match('/^(http:\/\/|https:\/\/|\/\/)/', $m)) {
                                                    $mSrc = $m;
                                                } elseif (str_starts_with($m, '/storage/')) {
                                                    $mSrc = asset($m);
                                                } else {
                                                    $mSrc = asset('storage/' . ltrim($m, '/'));
                                                }
                                            @endphp
                                            <img src="{{ $mSrc }}" alt="Materi Hafalan" class="mx-auto rounded">
                                        @else
                                            <p class="text-sm font-medium text-gray-800">
                                                {{ $ex0['memory_content'] ?? '' }}</p>
                                        @endif
                                    </div>
                                @endif

                                @php
                                    $ex = $exampleQuestions[0];
                                    $type = $ex['type'] ?? 'PILIHAN_GANDA';
                                    $correctAnswer = $ex['correct_answer'] ?? $ex['correct_answer_index'] ?? null;
                                @endphp
                                @if (($ex['type'] ?? '') === 'PAPIKOSTICK')
                                    <div class="space-y-3">
                                        <label
                                            class="example-option flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50"
                                            data-example="1" data-value="A">
                                            <input type="radio" name="example1" value="A"
                                                class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(1, 'A', null)">
                                            <span class="text-gray-700 flex-1"><strong>A.</strong>
                                                {{ $ex['statement_a'] ?? '' }}</span>
                                        </label>
                                        <label
                                            class="example-option flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50"
                                            data-example="1" data-value="B">
                                            <input type="radio" name="example1" value="B"
                                                class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(1, 'B', null)">
                                            <span class="text-gray-700 flex-1"><strong>B.</strong>
                                                {{ $ex['statement_b'] ?? '' }}</span>
                                        </label>
                                        <p class="mt-2 text-sm text-gray-500">Tidak ada jawaban benar atau salah. Pilih
                                            A atau B yang paling menggambarkan diri Anda.</p>
                                    </div>
                                @elseif(isset($exampleQuestions[0]['options']))
                                    <div class="space-y-3">
                                        @foreach ($ex['options'] as $index => $option)
                                            @php
                                                $isImage = preg_match('/\.(jpe?g|png|gif|webp)(\?.*)?$/i', $option);
                                                if (isset($option) && $isImage) {
                                                    if (preg_match('/^(http:\/\/|https:\/\/|\/\/)/', $option)) {
                                                        $src = $option;
                                                    } elseif (str_starts_with($option, '/storage/')) {
                                                        $src = asset($option);
                                                    } else {
                                                        $src = asset('storage/' . ltrim($option, '/'));
                                                    }
                                                }
                                            @endphp

                                            <label
                                                class="example-option flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50"
                                                data-example="1" data-value="{{ $index }}">
                                                @if ($type === 'PILIHAN_GANDA_KOMPLEKS')
                                                    <input type="checkbox" name="example1[]" value="{{ $index }}"
                                                        class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(1, {{ $index }}, {{ json_encode($correctAnswer) }})">
                                                @else
                                                    <input type="radio" name="example1" value="{{ $index }}"
                                                        class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(1, {{ $index }}, {{ json_encode($correctAnswer) }})">
                                                @endif

                                                <span class="text-gray-700 flex-1">
                                                    <strong>{{ chr(65 + $index) }}.</strong>
                                                    @if ($isImage)
                                                        <img src="{{ $src }}"
                                                            alt="Opsi {{ chr(65 + $index) }}"
                                                            class="max-h-48 mx-auto rounded">
                                                    @else
                                                        {{ $option }}
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Tombol Cek Jawaban -->
                                <div class="mt-4">
                                    <button type="button" onclick="showExampleFeedback(1)" id="btn-check-example1"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Cek Jawaban
                                    </button>
                                </div>
                            </div>

                            <!-- Feedback Section (Hidden initially) -->
                            <div id="feedback-example1" class="hidden">
                                @if (isset($exampleQuestions[0]['explanation']))
                                    <div id="explanation-example1" class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-4">
                                        <div class="flex items-start">
                                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="font-semibold text-blue-900 mb-1">Penjelasan:</p>
                                                <p class="text-blue-800">{{ $exampleQuestions[0]['explanation'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Answer Result -->
                                <div id="result-example1" class="p-4 rounded-lg mb-4"></div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <p class="text-gray-700">Contoh soal 1 belum tersedia.</p>
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                            <button onclick="showTab('tentang')"
                                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            @if($hasExample2)
                            <button onclick="showTab('contoh2')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Contoh Berikutnya
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            @else
                            <button onclick="showTab('kesiapan')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Lanjut ke Kesiapan
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- TAB 3: Contoh 2 -->
                    @if($hasExample2)
                    <div id="content-contoh2" class="tab-content hidden">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Contoh Soal 2
                            </h2>
                        </div>

                        @if (isset($exampleQuestions[1]))
                            <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                <p class="text-gray-800 font-medium text-lg mb-6">
                                    {{ $exampleQuestions[1]['question'] ?? 'Contoh soal tidak tersedia' }}
                                </p>

                                @php $ex1 = $exampleQuestions[1]; @endphp
                                @if (($ex1['type'] ?? '') === 'HAFALAN')
                                    <div class="bg-purple-100 border border-purple-300 rounded p-3 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-bold text-purple-900">📚 HAFALKAN
                                                ({{ $ex1['duration_seconds'] ?? 10 }} detik)</p>
                                        </div>
                                        @if (($ex1['memory_type'] ?? 'TEXT') === 'IMAGE')
                                            @php
                                                $m = $ex1['memory_content'] ?? '';
                                                if (preg_match('/^(http:\/\/|https:\/\/|\/\/)/', $m)) {
                                                    $mSrc = $m;
                                                } elseif (str_starts_with($m, '/storage/')) {
                                                    $mSrc = asset($m);
                                                } else {
                                                    $mSrc = asset('storage/' . ltrim($m, '/'));
                                                }
                                            @endphp
                                            <img src="{{ $mSrc }}" alt="Materi Hafalan"
                                                class="mx-auto rounded">
                                        @else
                                            <p class="text-sm font-medium text-gray-800">
                                                {{ $ex1['memory_content'] ?? '' }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if (isset($exampleQuestions[1]['options']))
                                    @php
                                        $ex = $exampleQuestions[1];
                                        $type = $ex['type'] ?? 'PILIHAN_GANDA';
                                        $correctAnswer2 = $ex['correct_answer'] ?? $ex['correct_answer_index'] ?? null;
                                    @endphp
                                    <div class="space-y-3">
                                        @foreach ($ex['options'] as $index => $option)
                                            @php
                                                $isImage = preg_match('/\.(jpe?g|png|gif|webp)(\?.*)?$/i', $option);
                                                if (isset($option) && $isImage) {
                                                    if (preg_match('/^(http:\/\/|https:\/\/|\/\/)/', $option)) {
                                                        $src = $option;
                                                    } elseif (str_starts_with($option, '/storage/')) {
                                                        $src = asset($option);
                                                    } else {
                                                        $src = asset('storage/' . ltrim($option, '/'));
                                                    }
                                                }
                                            @endphp

                                            <label
                                                class="example-option flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 border-gray-200 hover:border-indigo-300 hover:bg-indigo-50"
                                                data-example="2" data-value="{{ $index }}">
                                                @if ($type === 'PILIHAN_GANDA_KOMPLEKS')
                                                    <input type="checkbox" name="example2[]"
                                                        value="{{ $index }}"
                                                        class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(2, {{ $index }}, {{ json_encode($correctAnswer2) }})">
                                                @else
                                                    <input type="radio" name="example2"
                                                        value="{{ $index }}"
                                                        class="mt-1 mr-4 h-5 w-5 text-indigo-600" onchange="checkExampleAnswer(2, {{ $index }}, {{ json_encode($correctAnswer2) }})">
                                                @endif

                                                <span class="text-gray-700 flex-1">
                                                    <strong>{{ chr(65 + $index) }}.</strong>
                                                    @if ($isImage)
                                                        <img src="{{ $src }}"
                                                            alt="Opsi {{ chr(65 + $index) }}"
                                                            class="max-h-48 mx-auto rounded">
                                                    @else
                                                        {{ $option }}
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Tombol Cek Jawaban -->
                                <div class="mt-4">
                                    <button type="button" onclick="showExampleFeedback(2)" id="btn-check-example2"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Cek Jawaban
                                    </button>
                                </div>
                            </div>

                            <!-- Feedback Section (Hidden initially) -->
                            <div id="feedback-example2" class="hidden">
                                @if (isset($exampleQuestions[1]['explanation']))
                                    <div id="explanation-example2" class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-4">
                                        <div class="flex items-start">
                                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="font-semibold text-blue-900 mb-1">Penjelasan:</p>
                                                <p class="text-blue-800">{{ $exampleQuestions[1]['explanation'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Answer Result -->
                                <div id="result-example2" class="p-4 rounded-lg mb-4"></div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <p class="text-gray-700">Contoh soal 2 belum tersedia.</p>
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                            <button onclick="showTab('contoh1')"
                                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            <button onclick="showTab('kesiapan')"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Lanjut ke Kesiapan
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- TAB 4: Kesiapan -->
                    <div id="content-kesiapan" class="tab-content hidden">
                        <div class="text-center py-8">
                            <div
                                class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>

                            <h2 class="text-3xl font-bold text-gray-800 mb-4">Peringatan Penting</h2>

                            <div class="max-w-2xl mx-auto mb-8">
                                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6">
                                    <p class="text-red-700 font-semibold text-lg mb-3">
                                        ⚠️ Jangan ditutup terlebih dulu petunjuk ini, perhatikan contoh.
                                    </p>
                                    <p class="text-red-600 text-base">
                                        Apabila Anda klik tombol "Mulai Tes" di bawah, maka <strong>tes akan segera
                                            dimulai dan waktu akan berjalan</strong>.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <form
                                    action="{{ route('tests.alat.start', ['test' => $test->id, 'alat_tes' => $alatTes->id]) }}"
                                    method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-12 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mulai Tes Sekarang
                                    </button>
                                </form>

                                <div>
                                    <button onclick="showTab('contoh2')"
                                        class="text-gray-600 hover:text-gray-800 text-sm underline">
                                        ← Kembali ke Contoh Soal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Store selected answers for each example
        let selectedAnswers = {};
        let correctAnswers = {
            1: @json($exampleQuestions[0]['correct_answer'] ?? $exampleQuestions[0]['correct_answer_index'] ?? null),
            2: @json($exampleQuestions[1]['correct_answer'] ?? $exampleQuestions[1]['correct_answer_index'] ?? null)
        };

        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-indigo-600', 'text-gray-900', 'bg-gray-50');
                tab.classList.add('border-transparent', 'text-gray-600');
            });

            // Show selected tab content
            const contentEl = document.getElementById('content-' + tabName);
            if (contentEl) {
                contentEl.classList.remove('hidden');
            }

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) {
                activeTab.classList.add('border-indigo-600', 'text-gray-900', 'bg-gray-50');
                activeTab.classList.remove('border-transparent', 'text-gray-600');
            }
        }

        // Called when user selects an answer for example questions
        function checkExampleAnswer(exampleNum, value, correctAnswer) {
            selectedAnswers[exampleNum] = value;

            // Enable the check button
            const btnCheck = document.getElementById('btn-check-example' + exampleNum);
            if (btnCheck) {
                btnCheck.disabled = false;
            }
        }

        // Show feedback after user clicks "Cek Jawaban"
        function showExampleFeedback(exampleNum) {
            const feedbackEl = document.getElementById('feedback-example' + exampleNum);
            const resultEl = document.getElementById('result-example' + exampleNum);
            const correct = correctAnswers[exampleNum];
            const selected = selectedAnswers[exampleNum];

            if (!feedbackEl || !resultEl) return;

            // Show feedback section
            feedbackEl.classList.remove('hidden');

            // Check if answer is correct (for non-PAPI questions)
            if (correct !== null && correct !== undefined) {
                const isCorrect = (selected == correct) ||
                                  (Array.isArray(correct) && correct.includes(parseInt(selected)));

                if (isCorrect) {
                    resultEl.innerHTML = `
                        <div class="flex items-center bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-green-900">Jawaban Benar!</p>
                                <p class="text-green-800 text-sm">Selamat, jawaban Anda sudah tepat.</p>
                            </div>
                        </div>
                    `;
                } else {
                    const correctLetter = typeof correct === 'number' ? String.fromCharCode(65 + correct) : correct;
                    resultEl.innerHTML = `
                        <div class="flex items-center bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-red-900">Jawaban Salah</p>
                                <p class="text-red-800 text-sm">Jawaban yang benar adalah: <strong>${correctLetter}</strong></p>
                            </div>
                        </div>
                    `;
                }
            } else {
                // For PAPI questions (no correct answer)
                resultEl.innerHTML = `
                    <div class="flex items-center bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg">
                        <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-indigo-900">Jawaban Tersimpan</p>
                            <p class="text-indigo-800 text-sm">Tidak ada jawaban benar atau salah untuk soal ini. Pilih yang paling menggambarkan diri Anda.</p>
                        </div>
                    </div>
                `;
            }

            // Highlight the selected and correct options
            highlightOptions(exampleNum, selected, correct);

            // Disable the check button after showing feedback
            const btnCheck = document.getElementById('btn-check-example' + exampleNum);
            if (btnCheck) {
                btnCheck.disabled = true;
                btnCheck.textContent = 'Sudah Dicek';
            }

            // Scroll to feedback
            feedbackEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function highlightOptions(exampleNum, selected, correct) {
            const options = document.querySelectorAll(`.example-option[data-example="${exampleNum}"]`);

            options.forEach(option => {
                const value = option.dataset.value;
                option.classList.remove('border-gray-200', 'hover:border-indigo-300', 'hover:bg-indigo-50');

                if (correct !== null && correct !== undefined) {
                    if (value == correct || (Array.isArray(correct) && correct.includes(parseInt(value)))) {
                        // Correct answer - green
                        option.classList.add('border-green-500', 'bg-green-50');
                    } else if (value == selected) {
                        // Wrong selected answer - red
                        option.classList.add('border-red-500', 'bg-red-50');
                    } else {
                        option.classList.add('border-gray-200');
                    }
                } else {
                    // PAPI - just highlight selected
                    if (value == selected) {
                        option.classList.add('border-indigo-500', 'bg-indigo-50');
                    } else {
                        option.classList.add('border-gray-200');
                    }
                }
            });
        }
    </script>
</x-app-layout>
