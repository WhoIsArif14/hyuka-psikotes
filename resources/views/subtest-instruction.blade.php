<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petunjuk - {{ $alatTes->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto p-4 md:p-6">
        <!-- Header -->
        <div class="bg-gray-800 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h1 class="text-xl font-bold">Petunjuk</h1>
            <button onclick="window.history.back()" class="text-white hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="bg-white shadow-lg rounded-b-lg">
            <!-- Tabs -->
            <div class="grid grid-cols-4 border-b">
                <button onclick="showTab('tentang')" id="tab-tentang" class="tab-button py-3 px-4 text-sm font-medium border-b-2 border-green-600 text-gray-900">
                    Tentang Soal
                </button>
                <button onclick="showTab('contoh1')" id="tab-contoh1" class="tab-button py-3 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Contoh 1
                </button>
                <button onclick="showTab('contoh2')" id="tab-contoh2" class="tab-button py-3 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Contoh 2
                </button>
                <button onclick="showTab('kesiapan')" id="tab-kesiapan" class="tab-button py-3 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900">
                    Kesiapan
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Tentang Soal -->
                <div id="content-tentang" class="tab-content">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Tentang Soal</h2>
                    <div class="space-y-4 text-gray-700 leading-relaxed">
                        @if($alatTes->instructions)
                            {!! nl2br(e($alatTes->instructions)) !!}
                        @else
                            <p>Soal terdiri atas kalimat-kalimat.</p>
                            <p>Pada setiap kalimat satu kata hilang dan disediakan 5 (lima) kata pilihan sebagai penggantinya.</p>
                            <p>Pilihlah kata yang tepat yang dapat menyempurnakan kalimat itu!</p>
                        @endif
                    </div>
                </div>

                <!-- Contoh 1 -->
                <div id="content-contoh1" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Contoh 1</h2>
                    @if(isset($exampleQuestions[0]))
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <p class="text-gray-800 font-medium mb-4">{{ $exampleQuestions[0]['question'] ?? 'Contoh soal tidak tersedia' }}</p>
                            @if(isset($exampleQuestions[0]['options']))
                                <div class="space-y-2">
                                    @foreach($exampleQuestions[0]['options'] as $index => $option)
                                        <label class="flex items-center p-3 bg-white rounded border hover:bg-green-50 cursor-pointer {{ ($exampleQuestions[0]['correct_answer'] ?? -1) == $index ? 'border-green-500 bg-green-50' : '' }}">
                                            <input type="radio" name="example1" value="{{ $index }}" class="mr-3" {{ ($exampleQuestions[0]['correct_answer'] ?? -1) == $index ? 'checked' : '' }}>
                                            <span class="text-gray-700">{{ chr(65 + $index) }}. {{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(isset($exampleQuestions[0]['explanation']))
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 text-sm text-gray-700">
                                <strong>Penjelasan:</strong> {{ $exampleQuestions[0]['explanation'] }}
                            </div>
                        @endif
                    @else
                        <p class="text-gray-600">Contoh soal 1 belum tersedia.</p>
                    @endif
                </div>

                <!-- Contoh 2 -->
                <div id="content-contoh2" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Contoh 2</h2>
                    @if(isset($exampleQuestions[1]))
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <p class="text-gray-800 font-medium mb-4">{{ $exampleQuestions[1]['question'] ?? 'Contoh soal tidak tersedia' }}</p>
                            @if(isset($exampleQuestions[1]['options']))
                                <div class="space-y-2">
                                    @foreach($exampleQuestions[1]['options'] as $index => $option)
                                        <label class="flex items-center p-3 bg-white rounded border hover:bg-green-50 cursor-pointer {{ ($exampleQuestions[1]['correct_answer'] ?? -1) == $index ? 'border-green-500 bg-green-50' : '' }}">
                                            <input type="radio" name="example2" value="{{ $index }}" class="mr-3" {{ ($exampleQuestions[1]['correct_answer'] ?? -1) == $index ? 'checked' : '' }}>
                                            <span class="text-gray-700">{{ chr(65 + $index) }}. {{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(isset($exampleQuestions[1]['explanation']))
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 text-sm text-gray-700">
                                <strong>Penjelasan:</strong> {{ $exampleQuestions[1]['explanation'] }}
                            </div>
                        @endif
                    @else
                        <p class="text-gray-600">Contoh soal 2 belum tersedia.</p>
                    @endif
                </div>

                <!-- Kesiapan -->
                <div id="content-kesiapan" class="tab-content hidden">
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Peringatan</h2>
                        <p class="text-red-600 font-semibold text-lg mb-6">
                            Jangan ditutup terlebih dulu petunjuk ini, perhatikan contoh. 
                            Apabila petunjuk ini ditutup maka tes dimulai dan waktu berjalan.
                        </p>
                        
                        <form action="{{ route('tests.alat.start', ['test' => $test->id, 'alat_tes' => $alatTes->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-12 py-4 rounded-lg text-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                                Lanjut
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-green-600', 'text-gray-900');
                tab.classList.add('border-transparent', 'text-gray-600');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('border-green-600', 'text-gray-900');
            activeTab.classList.remove('border-transparent', 'text-gray-600');
        }
    </script>
</body>
</html>