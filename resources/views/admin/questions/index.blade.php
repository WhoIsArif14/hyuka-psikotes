<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Soal untuk Alat Tes: {{ $alatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Tambah Soal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Soal Baru</h3>
                    <button type="button" id="toggleFormBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        Tambah Soal
                    </button>
                </div>

                <div id="formContainer" style="display: none;">
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Whoops!</strong>
                            <span class="block sm:inline">Ada beberapa masalah dengan input Anda:</span>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.alat-tes.questions.store', ['alat_te' => $alatTes->id]) }}" id="questionForm">
                        @csrf

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="PILIHAN_GANDA" {{ old('type', 'PILIHAN_GANDA') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai</option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan</option>
                            </select>
                        </div>

                        <div id="question-text-container" class="mb-4">
                            <label for="question_text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                            <textarea id="question_text" name="question_text" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                        </div>

                        <div class="mb-2">
                            <div class="flex justify-between items-center">
                                <h4 class="text-md font-semibold text-gray-800">Opsi Jawaban</h4>
                                <button type="button" id="addOptionBtn" style="background: #22c55e; color: white; padding: 8px 16px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                                    ➕ Tambah Opsi
                                </button>
                            </div>
                        </div>

                        <div id="options-container" class="border border-gray-200 p-4 rounded-lg mb-4">
                            <div id="optionsList" class="space-y-3">
                                @for ($i = 0; $i < 4; $i++)
                                <div class="option-item flex items-start space-x-3 bg-gray-50 p-3 rounded-lg" data-index="{{ $i }}">
                                    <div class="flex items-center pt-2">
                                        <input type="radio" name="is_correct" value="{{ $i }}" class="h-4 w-4 text-green-600 correct-radio">
                                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500 option-label">Opsi {{ chr(65 + $i) }}</label>
                                        <input type="text" name="options[{{ $i }}][text]" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input" placeholder="Masukkan teks untuk Opsi {{ chr(65 + $i) }}">
                                        <input type="hidden" name="options[{{ $i }}][index]" value="{{ $i }}">
                                    </div>
                                    <button type="button" class="remove-option-btn text-red-500 hover:text-red-700 pt-2" style="{{ $i < 2 ? 'display: none;' : '' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <div id="memory-container" class="border border-indigo-200 p-4 rounded-lg mb-4 hidden">
                            <h4 class="text-md font-semibold text-indigo-700 mb-3">Materi Hafalan</h4>
                            <div class="mb-3">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten Memori</label>
                                <textarea id="memory_content" name="memory_content" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('memory_content') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                                <select id="memory_type" name="memory_type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="TEXT">Teks</option>
                                    <option value="IMAGE">Gambar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi Tampil (Detik)</label>
                                <input id="duration_seconds" name="duration_seconds" type="number" min="1" value="10" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Daftar Soal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Soal</h3>
                
                @if($questions->count() > 0)
                    <div class="space-y-4">
                        @foreach($questions as $index => $question)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                            {{ $question->type }}
                                        </span>
                                        <span class="text-gray-500 text-sm">Soal #{{ $questions->firstItem() + $index }}</span>
                                    </div>
                                    
                                    <p class="text-gray-800 font-medium mb-2">
                                        {{ $question->question_text ?: 'Materi Hafalan' }}
                                    </p>
                                    
                                    @if($question->type == 'PILIHAN_GANDA' && $question->options)
                                        @php
                                            $opts = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                        @endphp
                                        <div class="ml-4 space-y-1 text-sm text-gray-600">
                                            @foreach($opts as $key => $opt)
                                                <div class="flex items-center">
                                                    <span class="font-semibold mr-2">{{ chr(65 + $key) }}.</span>
                                                    <span>{{ $opt['text'] ?? '' }}</span>
                                                    @if($question->correct_answer_index == $key)
                                                        <span class="ml-2 text-green-600 text-xs">✓ Benar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2 ml-4">
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>Belum ada soal. Klik "Tambah Soal" untuk membuat soal pertama.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleFormBtn = document.getElementById('toggleFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const formContainer = document.getElementById('formContainer');
            const typeSelect = document.getElementById('type');
            const optionsContainer = document.getElementById('options-container');
            const memoryContainer = document.getElementById('memory-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');
            
            let optionCount = 4;

            toggleFormBtn.addEventListener('click', function() {
                formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
                toggleFormBtn.textContent = formContainer.style.display === 'none' ? 'Tambah Soal' : 'Sembunyikan Form';
            });

            cancelBtn.addEventListener('click', function() {
                formContainer.style.display = 'none';
                toggleFormBtn.textContent = 'Tambah Soal';
            });

            function toggleContainers() {
                const selectedType = typeSelect.value;
                optionsContainer.style.display = 'none';
                memoryContainer.classList.add('hidden');
                questionTextContainer.style.display = 'none';
                addOptionBtn.style.display = 'none';

                if (selectedType === 'PILIHAN_GANDA' || selectedType === 'ESSAY') {
                    questionTextContainer.style.display = 'block';
                }
                
                if (selectedType === 'PILIHAN_GANDA') {
                    optionsContainer.style.display = 'block';
                    addOptionBtn.style.display = 'inline-block';
                } else if (selectedType === 'HAFALAN') {
                    memoryContainer.classList.remove('hidden');
                }
            }

            function updateOptionLabels() {
                document.querySelectorAll('.option-item').forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const radio = item.querySelector('.correct-radio');
                    const removeBtn = item.querySelector('.remove-option-btn');
                    
                    const letter = String.fromCharCode(65 + index);
                    label.textContent = `Opsi ${letter}`;
                    input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                    input.name = `options[${index}][text]`;
                    radio.value = index;
                    
                    removeBtn.style.display = optionCount > 2 ? 'block' : 'none';
                });
            }

            addOptionBtn.addEventListener('click', function() {
                const newIndex = optionCount;
                const letter = String.fromCharCode(65 + newIndex);
                
                const newOption = document.createElement('div');
                newOption.className = 'option-item flex items-start space-x-3 bg-gray-50 p-3 rounded-lg';
                newOption.innerHTML = `
                    <div class="flex items-center pt-2">
                        <input type="radio" name="is_correct" value="${newIndex}" class="h-4 w-4 text-green-600 correct-radio">
                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 option-label">Opsi ${letter}</label>
                        <input type="text" name="options[${newIndex}][text]" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input" placeholder="Masukkan teks untuk Opsi ${letter}">
                        <input type="hidden" name="options[${newIndex}][index]" value="${newIndex}">
                    </div>
                    <button type="button" class="remove-option-btn text-red-500 hover:text-red-700 pt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                
                optionsList.appendChild(newOption);
                optionCount++;
                updateOptionLabels();
            });

            optionsList.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-option-btn');
                if (removeBtn && optionCount > 2) {
                    removeBtn.closest('.option-item').remove();
                    optionCount--;
                    updateOptionLabels();
                }
            });

            typeSelect.addEventListener('change', toggleContainers);
            toggleContainers();

            @if($errors->any())
                formContainer.style.display = 'block';
                toggleFormBtn.textContent = 'Sembunyikan Form';
            @endif
        });
    </script>
</x-admin-layout>