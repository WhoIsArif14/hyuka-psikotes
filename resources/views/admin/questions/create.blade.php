<x-admin-layout>
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Tambah Soal Baru - {{ $AlatTes->name }}</h2>
            <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}" 
               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                ← Kembali
            </a>
        </div>

        {{-- Display Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan!</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.alat-tes.questions.store', $AlatTes->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              id="form-soal">
            @csrf

            <!-- Tipe Pertanyaan -->
            <div class="mb-6">
                <label for="type" class="block font-semibold mb-2 text-gray-700">
                    Tipe Pertanyaan <span class="text-red-500">*</span>
                </label>
                <select name="type" 
                        id="type" 
                        class="border-gray-300 rounded-lg w-full p-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="PILIHAN_GANDA" {{ old('type') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="PILIHAN_GANDA_KOMPLEKS" {{ old('type') == 'PILIHAN_GANDA_KOMPLEKS' ? 'selected' : '' }}>Pilihan Ganda Kompleks</option>
                    <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Essay</option>
                    <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Gambar Pertanyaan -->
            <div class="mb-6">
                <label class="block font-semibold mb-2 text-gray-700">
                    Upload Gambar Pertanyaan (Opsional)
                </label>
                <input type="file" 
                       name="question_image" 
                       id="question_image"
                       accept=".jpg,.jpeg,.png,.gif"
                       class="border border-gray-300 rounded-lg p-2 w-full focus:ring-indigo-500 focus:border-indigo-500 @error('question_image') border-red-500 @enderror">
                <small class="text-gray-500 block mt-1">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                @error('question_image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                
                <!-- Preview Gambar -->
                <div id="imagePreview" class="mt-3 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview:</p>
                    <img src="" alt="Preview" class="rounded-lg border max-w-md">
                </div>
            </div>

            <!-- Teks Pertanyaan -->
            <div class="mb-6" id="questionTextSection">
                <label class="block font-semibold mb-2 text-gray-700">
                    Teks Pertanyaan <span id="requiredStar" class="text-red-500 hidden">*</span>
                </label>
                <textarea name="question_text" 
                          id="question_text"
                          rows="4" 
                          class="border-gray-300 rounded-lg w-full p-3 focus:ring-indigo-500 focus:border-indigo-500 @error('question_text') border-red-500 @enderror" 
                          placeholder="Masukkan teks pertanyaan di sini...">{{ old('question_text') }}</textarea>
                <small class="text-gray-500 block mt-1">Opsional jika sudah ada gambar pertanyaan</small>
                @error('question_text')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opsi Jawaban (Pilihan Ganda) -->
            <div id="opsi-container" class="mb-6 hidden">
                <label class="block font-semibold mb-3 text-gray-700">
                    Opsi Jawaban <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-4">
                    ⚠️ Minimal 2 opsi harus diisi. Pilih salah satu sebagai jawaban benar dengan mencentang radio button.
                </p>

                <div id="options-wrapper">
                    <!-- Opsi A -->
                    <div class="opsi-item border-2 border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:border-indigo-300 transition">
                        <div class="flex items-center mb-3">
                            <input type="radio" 
                                   name="is_correct" 
                                   value="0" 
                                   id="correct_0"
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-3"
                                   {{ old('is_correct') == '0' ? 'checked' : '' }}>
                            <label for="correct_0" class="font-semibold text-gray-700 flex-1">
                                Opsi A - <span class="text-sm text-gray-500 font-normal">Centang jika ini jawaban benar</span>
                            </label>
                        </div>
                        <input type="text" 
                               name="options[0][text]" 
                               class="border-gray-300 rounded-lg w-full mb-2 p-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan teks untuk Opsi A"
                               value="{{ old('options.0.text') }}">
                        <input type="hidden" name="options[0][index]" value="0">
                        <input type="file" 
                               name="options[0][image_file]" 
                               accept=".jpg,.jpeg,.png,.gif,.webp"
                               class="border-gray-300 rounded-lg w-full p-2">
                        <small class="text-gray-500 block mt-1">Opsional: upload gambar untuk opsi ini</small>
                    </div>

                    <!-- Opsi B -->
                    <div class="opsi-item border-2 border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:border-indigo-300 transition">
                        <div class="flex items-center mb-3">
                            <input type="radio" 
                                   name="is_correct" 
                                   value="1" 
                                   id="correct_1"
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-3"
                                   {{ old('is_correct') == '1' ? 'checked' : '' }}>
                            <label for="correct_1" class="font-semibold text-gray-700 flex-1">
                                Opsi B - <span class="text-sm text-gray-500 font-normal">Centang jika ini jawaban benar</span>
                            </label>
                        </div>
                        <input type="text" 
                               name="options[1][text]" 
                               class="border-gray-300 rounded-lg w-full mb-2 p-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan teks untuk Opsi B"
                               value="{{ old('options.1.text') }}">
                        <input type="hidden" name="options[1][index]" value="1">
                        <input type="file" 
                               name="options[1][image_file]" 
                               accept=".jpg,.jpeg,.png,.gif,.webp"
                               class="border-gray-300 rounded-lg w-full p-2">
                        <small class="text-gray-500 block mt-1">Opsional: upload gambar untuk opsi ini</small>
                    </div>

                    <!-- Opsi C -->
                    <div class="opsi-item border-2 border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:border-indigo-300 transition">
                        <div class="flex items-center mb-3">
                            <input type="radio" 
                                   name="is_correct" 
                                   value="2" 
                                   id="correct_2"
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-3"
                                   {{ old('is_correct') == '2' ? 'checked' : '' }}>
                            <label for="correct_2" class="font-semibold text-gray-700 flex-1">
                                Opsi C - <span class="text-sm text-gray-500 font-normal">Centang jika ini jawaban benar</span>
                            </label>
                        </div>
                        <input type="text" 
                               name="options[2][text]" 
                               class="border-gray-300 rounded-lg w-full mb-2 p-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan teks untuk Opsi C"
                               value="{{ old('options.2.text') }}">
                        <input type="hidden" name="options[2][index]" value="2">
                        <input type="file" 
                               name="options[2][image_file]" 
                               accept=".jpg,.jpeg,.png,.gif,.webp"
                               class="border-gray-300 rounded-lg w-full p-2">
                        <small class="text-gray-500 block mt-1">Opsional: upload gambar untuk opsi ini</small>
                    </div>

                    <!-- Opsi D -->
                    <div class="opsi-item border-2 border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:border-indigo-300 transition">
                        <div class="flex items-center mb-3">
                            <input type="radio" 
                                   name="is_correct" 
                                   value="3" 
                                   id="correct_3"
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-3"
                                   {{ old('is_correct') == '3' ? 'checked' : '' }}>
                            <label for="correct_3" class="font-semibold text-gray-700 flex-1">
                                Opsi D - <span class="text-sm text-gray-500 font-normal">Centang jika ini jawaban benar</span>
                            </label>
                        </div>
                        <input type="text" 
                               name="options[3][text]" 
                               class="border-gray-300 rounded-lg w-full mb-2 p-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan teks untuk Opsi D"
                               value="{{ old('options.3.text') }}">
                        <input type="hidden" name="options[3][index]" value="3">
                        <input type="file" 
                               name="options[3][image_file]" 
                               accept=".jpg,.jpeg,.png,.gif,.webp"
                               class="border-gray-300 rounded-lg w-full p-2">
                        <small class="text-gray-500 block mt-1">Opsional: upload gambar untuk opsi ini</small>
                    </div>
                </div>

                <button type="button" 
                        id="add-option" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                    + Tambah Opsi Lainnya
                </button>

                @error('is_correct')
                    <p class="text-red-500 text-sm mt-2 font-semibold">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <!-- Contoh Soal -->
            <div class="mb-6">
                <label class="block font-semibold mb-2 text-gray-700">
                    Contoh Soal (Opsional)
                </label>
                <textarea name="example_question" 
                          rows="3" 
                          class="border-gray-300 rounded-lg w-full p-3 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Contoh: Siapa presiden pertama Indonesia? Jawab: Soekarno">{{ old('example_question') }}</textarea>
            </div>

            <!-- Instruksi -->
            <div class="mb-6">
                <label class="block font-semibold mb-2 text-gray-700">
                    Instruksi Pengerjaan (Opsional)
                </label>
                <textarea name="instructions" 
                          rows="2" 
                          class="border-gray-300 rounded-lg w-full p-3 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Instruksi khusus untuk soal ini...">{{ old('instructions') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}" 
                   class="px-6 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition">
                    Batal
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    💾 Simpan Pertanyaan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const opsiContainer = document.getElementById('opsi-container');
            const questionTextSection = document.getElementById('questionTextSection');
            const requiredStar = document.getElementById('requiredStar');
            const formSoal = document.getElementById('form-soal');
            const submitBtn = document.getElementById('submitBtn');
            const addOptionBtn = document.getElementById('add-option');
            const optionsWrapper = document.getElementById('options-wrapper');
            let optionCount = 4; // Sudah ada 4 opsi default

            // Handle type change
            typeSelect.addEventListener('change', function() {
                const type = this.value;
                console.log('Type changed to:', type);
                
                if (type === 'PILIHAN_GANDA' || type === 'PILIHAN_GANDA_KOMPLEKS') {
                    opsiContainer.classList.remove('hidden');
                    requiredStar.classList.add('hidden');
                } else {
                    opsiContainer.classList.add('hidden');
                    if (type === 'ESSAY') {
                        requiredStar.classList.remove('hidden');
                    } else {
                        requiredStar.classList.add('hidden');
                    }
                }
            });

            // Trigger on page load if there's old input
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }

            // Add new option
            addOptionBtn.addEventListener('click', function() {
                const label = String.fromCharCode(65 + optionCount); // E, F, G...
                const newOption = document.createElement('div');
                newOption.className = 'opsi-item border-2 border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:border-indigo-300 transition';
                newOption.innerHTML = `
                    <div class="flex items-center mb-3">
                        <input type="radio" 
                               name="is_correct" 
                               value="${optionCount}" 
                               id="correct_${optionCount}"
                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-3">
                        <label for="correct_${optionCount}" class="font-semibold text-gray-700 flex-1">
                            Opsi ${label} - <span class="text-sm text-gray-500 font-normal">Centang jika ini jawaban benar</span>
                        </label>
                        <button type="button" class="remove-option text-red-500 hover:text-red-700 font-bold text-xl">
                            ✕
                        </button>
                    </div>
                    <input type="text" 
                           name="options[${optionCount}][text]" 
                           class="border-gray-300 rounded-lg w-full mb-2 p-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Masukkan teks untuk Opsi ${label}">
                    <input type="hidden" name="options[${optionCount}][index]" value="${optionCount}">
                    <input type="file" 
                           name="options[${optionCount}][image_file]" 
                           accept=".jpg,.jpeg,.png,.gif,.webp"
                           class="border-gray-300 rounded-lg w-full p-2">
                    <small class="text-gray-500 block mt-1">Opsional: upload gambar untuk opsi ini</small>
                `;
                
                optionsWrapper.appendChild(newOption);
                optionCount++;

                // Add remove handler
                newOption.querySelector('.remove-option').addEventListener('click', function() {
                    if (confirm('Hapus opsi ini?')) {
                        newOption.remove();
                        updateOptionLabels();
                    }
                });
            });

            // Update labels after removal
            function updateOptionLabels() {
                const items = document.querySelectorAll('.opsi-item');
                items.forEach((item, idx) => {
                    const label = String.fromCharCode(65 + idx);
                    const labelElement = item.querySelector('label');
                    if (labelElement) {
                        const currentText = labelElement.innerHTML;
                        labelElement.innerHTML = currentText.replace(/Opsi [A-Z]/, `Opsi ${label}`);
                    }
                });
            }

            // Image preview for question image
            document.getElementById('question_image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagePreview');
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                }
            });

            // Form validation before submit
            formSoal.addEventListener('submit', function(e) {
                const type = typeSelect.value;
                
                // Check if type is selected
                if (!type) {
                    e.preventDefault();
                    alert('⚠️ Pilih tipe pertanyaan terlebih dahulu!');
                    typeSelect.focus();
                    return false;
                }

                // Validation for multiple choice
                if (type === 'PILIHAN_GANDA' || type === 'PILIHAN_GANDA_KOMPLEKS') {
                    // Check if correct answer is selected
                    const isCorrectChecked = document.querySelector('input[name="is_correct"]:checked');
                    
                    if (!isCorrectChecked) {
                        e.preventDefault();
                        alert('⚠️ Anda harus memilih satu jawaban yang benar!\n\nCentang salah satu radio button di sebelah kiri opsi.');
                        return false;
                    }

                    // Check if at least 2 options have text
                    const optionTexts = document.querySelectorAll('input[name^="options"][name$="[text]"]');
                    let filledOptions = 0;
                    optionTexts.forEach(input => {
                        if (input.value.trim() !== '') {
                            filledOptions++;
                        }
                    });

                    if (filledOptions < 2) {
                        e.preventDefault();
                        alert('⚠️ Minimal harus ada 2 opsi jawaban yang diisi!\n\nIsi minimal Opsi A dan Opsi B.');
                        return false;
                    }

                    console.log('Validation passed! Submitting form...');
                    console.log('Selected correct answer:', isCorrectChecked.value);
                    console.log('Filled options:', filledOptions);
                }

                // Disable submit button to prevent double submit
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Menyimpan...';
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            // Handle remove option for dynamically added options
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-option')) {
                    if (confirm('Hapus opsi ini?')) {
                        e.target.closest('.opsi-item').remove();
                        updateOptionLabels();
                    }
                }
            });

            console.log('Form script loaded successfully');
        });
    </script>
</x-admin-layout>