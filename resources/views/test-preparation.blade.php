<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Petunjuk Pengerjaan - {{ $alatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                <!-- Tabs Navigation -->
                <div class="grid grid-cols-4 border-b">
                    <button onclick="showTab('tentang')" id="tab-tentang" 
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-indigo-600 text-gray-900 bg-gray-50">
                        Tentang Soal
                    </button>
                    <button onclick="showTab('contoh1')" id="tab-contoh1" 
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        Contoh 1
                    </button>
                    <button onclick="showTab('contoh2')" id="tab-contoh2" 
                        class="tab-button py-4 px-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        Contoh 2
                    </button>
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
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Tentang Soal
                            </h2>
                        </div>
                        
                        <div class="prose max-w-none">
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

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <button onclick="showTab('contoh1')" 
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Lihat Contoh Soal
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: Contoh 1 -->
                    <div id="content-contoh1" class="tab-content hidden">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Contoh Soal 1
                            </h2>
                        </div>

                        @if(isset($exampleQuestions[0]))
                            <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                <p class="text-gray-800 font-medium text-lg mb-6">
                                    {{ $exampleQuestions[0]['question'] ?? 'Contoh soal tidak tersedia' }}
                                </p>
                                
                                @if(isset($exampleQuestions[0]['options']))
                                    <div class="space-y-3">
                                        @foreach($exampleQuestions[0]['options'] as $index => $option)
                                            <label class="flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 {{ ($exampleQuestions[0]['correct_answer'] ?? -1) == $index ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50' }}">
                                                <input type="radio" name="example1" value="{{ $index }}" 
                                                    class="mt-1 mr-4 h-5 w-5 text-indigo-600" 
                                                    {{ ($exampleQuestions[0]['correct_answer'] ?? -1) == $index ? 'checked' : '' }} disabled>
                                                <span class="text-gray-700 flex-1">
                                                    <strong>{{ chr(65 + $index) }}.</strong> {{ $option }}
                                                </span>
                                                @if(($exampleQuestions[0]['correct_answer'] ?? -1) == $index)
                                                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            @if(isset($exampleQuestions[0]['explanation']))
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-blue-900 mb-1">Penjelasan:</p>
                                            <p class="text-blue-800">{{ $exampleQuestions[0]['explanation'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-gray-700">Contoh soal 1 belum tersedia.</p>
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                            <button onclick="showTab('tentang')" 
                                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            <button onclick="showTab('contoh2')" 
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Contoh Berikutnya
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 3: Contoh 2 -->
                    <div id="content-contoh2" class="tab-content hidden">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Contoh Soal 2
                            </h2>
                        </div>

                        @if(isset($exampleQuestions[1]))
                            <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                <p class="text-gray-800 font-medium text-lg mb-6">
                                    {{ $exampleQuestions[1]['question'] ?? 'Contoh soal tidak tersedia' }}
                                </p>
                                
                                @if(isset($exampleQuestions[1]['options']))
                                    <div class="space-y-3">
                                        @foreach($exampleQuestions[1]['options'] as $index => $option)
                                            <label class="flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer transition-all duration-200 {{ ($exampleQuestions[1]['correct_answer'] ?? -1) == $index ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50' }}">
                                                <input type="radio" name="example2" value="{{ $index }}" 
                                                    class="mt-1 mr-4 h-5 w-5 text-indigo-600" 
                                                    {{ ($exampleQuestions[1]['correct_answer'] ?? -1) == $index ? 'checked' : '' }} disabled>
                                                <span class="text-gray-700 flex-1">
                                                    <strong>{{ chr(65 + $index) }}.</strong> {{ $option }}
                                                </span>
                                                @if(($exampleQuestions[1]['correct_answer'] ?? -1) == $index)
                                                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            @if(isset($exampleQuestions[1]['explanation']))
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-blue-900 mb-1">Penjelasan:</p>
                                            <p class="text-blue-800">{{ $exampleQuestions[1]['explanation'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-gray-700">Contoh soal 2 belum tersedia.</p>
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                            <button onclick="showTab('contoh1')" 
                                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </button>
                            <button onclick="showTab('kesiapan')" 
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200">
                                Lanjut ke Kesiapan
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 4: Kesiapan -->
                    <div id="content-kesiapan" class="tab-content hidden">
                        <div class="text-center py-8">
                            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">Peringatan Penting</h2>
                            
                            <div class="max-w-2xl mx-auto mb-8">
                                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6">
                                    <p class="text-red-700 font-semibold text-lg mb-3">
                                        ⚠️ Jangan ditutup terlebih dulu petunjuk ini, perhatikan contoh.
                                    </p>
                                    <p class="text-red-600 text-base">
                                        Apabila Anda klik tombol "Mulai Tes" di bawah, maka <strong>tes akan segera dimulai dan waktu akan berjalan</strong>.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <form action="{{ route('tests.alat.start', ['test' => $test->id, 'alat_tes' => $alatTes->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                        class="inline-flex items-center px-12 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('border-indigo-600', 'text-gray-900', 'bg-gray-50');
            activeTab.classList.remove('border-transparent', 'text-gray-600');
        }
    </script>
</x-app-layout>