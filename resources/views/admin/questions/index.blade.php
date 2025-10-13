<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Soal untuk Alat Tes: {{ $AlatTes->name }}
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

                    <form method="POST" action="{{ route('admin.alat-tes.questions.store', ['alat_te' => $AlatTes->id]) }}" id="questionForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="PILIHAN_GANDA" {{ old('type', 'PILIHAN_GANDA') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai</option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan</option>
                            </select>
                        </div>

                        {{-- Upload Gambar Pertanyaan --}}
                        <div class="mb-4" id="question-image-container">
                            <label for="question_image" class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Gambar Pertanyaan (Opsional)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="file" 
                                       id="question_image" 
                                       name="question_image" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="button" id="removeImageBtn" style="display: none;" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Hapus
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-300">
                            </div>
                            
                            @error('question_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- MATERI HAFALAN CONTAINER --}}
                        <div id="memory-container" class="border border-indigo-200 bg-indigo-50 p-4 rounded-lg mb-4 hidden">
                            <h4 class="text-md font-semibold text-indigo-700 mb-3">📚 Materi Hafalan</h4>
                            <p class="text-sm text-gray-600 mb-3">Materi ini akan ditampilkan selama beberapa detik, kemudian siswa akan menjawab pertanyaan di bawah.</p>
                            
                            <div class="mb-3">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten Memori</label>
                                <textarea id="memory_content" name="memory_content" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Contoh: BUNGA - Dahlia, Flamboyan, Laret, Soka, Yasmin&#10;PERKAKAS - Cangkul, Jarum, Kikir, Palu, Wajan">{{ old('memory_content') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Masukkan materi yang harus dihafal siswa</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                                    <select id="memory_type" name="memory_type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                        <option value="TEXT" {{ old('memory_type', 'TEXT') == 'TEXT' ? 'selected' : '' }}>Teks</option>
                                        <option value="IMAGE" {{ old('memory_type') == 'IMAGE' ? 'selected' : '' }}>Gambar</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi Tampil (Detik)</label>
                                    <input id="duration_seconds" name="duration_seconds" type="number" min="1" value="{{ old('duration_seconds', 10) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">Berapa lama materi ditampilkan</p>
                                </div>
                            </div>
                        </div>

                        {{-- TEKS PERTANYAAN (setelah hafalan) --}}
                        <div id="question-text-container" class="mb-4">
                            <label for="question_text" class="block text-sm font-medium text-gray-700">
                                <span id="question-text-label">Teks Pertanyaan</span>
                            </label>
                            <textarea id="question_text" name="question_text" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1" id="question-text-hint">Pertanyaan untuk siswa</p>
                        </div>

                        {{-- OPSI JAWABAN --}}
                        <div id="options-section" class="mb-4">
                            <div class="mb-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800">Opsi Jawaban</h4>
                                        <p class="text-xs text-gray-500" id="options-hint">Pilihan jawaban untuk pertanyaan</p>
                                    </div>
                                    <button type="button" id="addOptionBtn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                        ➕ Tambah Opsi
                                    </button>
                                </div>
                            </div>

                            <div id="options-container" class="border border-gray-200 p-4 rounded-lg">
                                <div id="optionsList" class="space-y-3">
                                    @for ($i = 0; $i < 4; $i++)
                                    <div class="option-item bg-gray-50 p-3 rounded-lg" data-index="{{ $i }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex items-center pt-2">
                                                <input type="radio" name="is_correct" value="{{ $i }}" class="h-4 w-4 text-green-600 correct-radio" {{ old('is_correct') == $i ? 'checked' : '' }}>
                                                <label class="ml-2 text-sm text-gray-600">Benar</label>
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-gray-500 option-label">Opsi {{ chr(65 + $i) }}</label>
                                                <input type="text" name="options[{{ $i }}][text]" value="{{ old('options.'.$i.'.text') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input" placeholder="Masukkan teks untuk Opsi {{ chr(65 + $i) }}">
                                                <input type="hidden" name="options[{{ $i }}][index]" value="{{ $i }}">
                                                
                                                {{-- Upload Gambar untuk Opsi --}}
                                                <div class="mt-3">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Upload Gambar Opsi (Opsional)</label>
                                                    <input type="file" 
                                                           name="options[{{ $i }}][image_file]" 
                                                           accept="image/*"
                                                           class="option-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                    <div class="option-image-preview mt-2" style="display: none;">
                                                        <img src="" alt="Preview Opsi" class="max-w-xs max-h-32 rounded border border-gray-300 option-preview-img">
                                                        <button type="button" class="remove-option-image-btn text-red-600 hover:text-red-800 text-xs font-medium mt-1">
                                                            Hapus Gambar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-option-btn text-red-500 hover:text-red-700 pt-2" style="{{ $i < 2 ? 'display: none;' : '' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
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
                                        
                                        @if($question->type == 'HAFALAN')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">
                                                ⏱️ {{ $question->duration_seconds }}s
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Tampilkan Materi Hafalan --}}
                                    @if($question->type == 'HAFALAN' && $question->memory_content)
                                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 mb-3">
                                            <p class="text-xs font-semibold text-indigo-700 mb-1">📚 Materi Hafalan ({{ $question->memory_type }}):</p>
                                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $question->memory_content }}</p>
                                        </div>
                                    @endif
                                    
                                    <p class="text-gray-800 font-medium mb-2">
                                        {{ $question->question_text ?: 'Materi Hafalan' }}
                                    </p>

                                    {{-- Tampilkan Gambar jika ada --}}
                                    @if($question->image_path)
                                        <div class="my-3">
                                            <img src="{{ asset('storage/' . $question->image_path) }}" 
                                                 alt="Question Image" 
                                                 class="max-w-sm max-h-64 rounded-lg border border-gray-200 shadow-sm object-contain">
                                        </div>
                                    @endif
                                    
                                    @if(($question->type == 'PILIHAN_GANDA' || $question->type == 'HAFALAN') && $question->options)
                                        @php
                                            $opts = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                        @endphp
                                        <div class="ml-4 space-y-2 text-sm text-gray-600">
                                            @foreach($opts as $key => $opt)
                                                <div class="flex items-start">
                                                    <span class="font-semibold mr-2">{{ chr(65 + $key) }}.</span>
                                                    <div class="flex-1">
                                                        <span>{{ $opt['text'] ?? '' }}</span>
                                                        @if(isset($opt['image_path']) && $opt['image_path'])
                                                            <div class="mt-1">
                                                                <img src="{{ asset('storage/' . $opt['image_path']) }}" 
                                                                     alt="Opsi {{ chr(65 + $key) }}" 
                                                                     class="max-w-xs max-h-32 rounded border border-gray-200">
                                                            </div>
                                                        @endif
                                                    </div>
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
            const optionsSection = document.getElementById('options-section');
            const memoryContainer = document.getElementById('memory-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionImageContainer = document.getElementById('question-image-container');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');
            
            // Image preview
            const questionImage = document.getElementById('question_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');
            
            let optionCount = 4;

            // Image preview functionality for question image
            questionImage.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                        removeImageBtn.style.display = 'inline-block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            removeImageBtn.addEventListener('click', function() {
                questionImage.value = '';
                imagePreview.style.display = 'none';
                removeImageBtn.style.display = 'none';
            });

            // Handle option image preview
            function setupOptionImagePreview(optionItem) {
                const imageInput = optionItem.querySelector('.option-image-input');
                const previewContainer = optionItem.querySelector('.option-image-preview');
                const previewImg = optionItem.querySelector('.option-preview-img');
                const removeBtn = optionItem.querySelector('.remove-option-image-btn');
                
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewContainer.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    }
                });
                
                removeBtn.addEventListener('click', function() {
                    imageInput.value = '';
                    previewContainer.style.display = 'none';
                });
            }

            // Setup initial option image previews
            document.querySelectorAll('.option-item').forEach(item => {
                setupOptionImagePreview(item);
            });

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
                
                // Default: sembunyikan semua
                optionsSection.style.display = 'none';
                memoryContainer.classList.add('hidden');
                questionTextContainer.style.display = 'none';
                questionImageContainer.style.display = 'none';

                // Update label dan hint
                const questionTextLabel = document.getElementById('question-text-label');
                const questionTextHint = document.getElementById('question-text-hint');
                const optionsHint = document.getElementById('options-hint');

                if (selectedType === 'PILIHAN_GANDA') {
                    questionTextContainer.style.display = 'block';
                    questionImageContainer.style.display = 'block';
                    optionsSection.style.display = 'block';
                    
                    questionTextLabel.textContent = 'Teks Pertanyaan';
                    questionTextHint.textContent = 'Pertanyaan untuk siswa';
                    optionsHint.textContent = 'Pilihan jawaban untuk pertanyaan';
                    
                } else if (selectedType === 'ESSAY') {
                    questionTextContainer.style.display = 'block';
                    questionImageContainer.style.display = 'block';
                    
                } else if (selectedType === 'HAFALAN') {
                    // ✅ PERBAIKAN: Untuk HAFALAN tampilkan SEMUA
                    memoryContainer.classList.remove('hidden');
                    questionTextContainer.style.display = 'block';
                    optionsSection.style.display = 'block';
                    questionImageContainer.style.display = 'none'; // Gambar optional di materi hafalan
                    
                    questionTextLabel.textContent = '❓ Pertanyaan (setelah hafalan)';
                    questionTextHint.textContent = 'Pertanyaan yang akan muncul setelah materi hafalan hilang';
                    optionsHint.textContent = 'Pilihan jawaban untuk pertanyaan setelah hafalan';
                }
            }

            function updateOptionLabels() {
                document.querySelectorAll('.option-item').forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const radio = item.querySelector('.correct-radio');
                    const removeBtn = item.querySelector('.remove-option-btn');
                    const imageInput = item.querySelector('.option-image-input');
                    
                    const letter = String.fromCharCode(65 + index);
                    label.textContent = `Opsi ${letter}`;
                    input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                    input.name = `options[${index}][text]`;
                    imageInput.name = `options[${index}][image_file]`;
                    radio.value = index;
                    
                    removeBtn.style.display = optionCount > 2 ? 'block' : 'none';
                });
            }

            addOptionBtn.addEventListener('click', function() {
                const newIndex = optionCount;
                const letter = String.fromCharCode(65 + newIndex);
                
                const newOption = document.createElement('div');
                newOption.className = 'option-item bg-gray-50 p-3 rounded-lg';
                newOption.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center pt-2">
                            <input type="radio" name="is_correct" value="${newIndex}" class="h-4 w-4 text-green-600 correct-radio">
                            <label class="ml-2 text-sm text-gray-600">Benar</label>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 option-label">Opsi ${letter}</label>
                            <input type="text" name="options[${newIndex}][text]" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input" placeholder="Masukkan teks untuk Opsi ${letter}">
                            <input type="hidden" name="options[${newIndex}][index]" value="${newIndex}">
                            
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Upload Gambar Opsi (Opsional)</label>
                                <input type="file" 
                                       name="options[${newIndex}][image_file]" 
                                       accept="image/*"
                                       class="option-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <div class="option-image-preview mt-2" style="display: none;">
                                    <img src="" alt="Preview Opsi" class="max-w-xs max-h-32 rounded border border-gray-300 option-preview-img">
                                    <button type="button" class="remove-option-image-btn text-red-600 hover:text-red-800 text-xs font-medium mt-1">
                                        Hapus Gambar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="remove-option-btn text-red-500 hover:text-red-700 pt-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
                
                optionsList.appendChild(newOption);
                setupOptionImagePreview(newOption);
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