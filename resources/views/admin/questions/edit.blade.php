<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal: ') }}{{ $question->type }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

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

                <form method="POST"
                    action="{{ route('admin.alat-tes.questions.update', ['alat_te' => $alat_te->id, 'question' => $question->id]) }}"
                    enctype="multipart/form-data" id="editForm">
                    @csrf
                    @method('PUT')

                    {{-- Tipe Pertanyaan (Read Only) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                        <input type="text" value="{{ $question->type }}" disabled
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm">
                        <input type="hidden" name="type" value="{{ $question->type }}" id="questionType">
                        <p class="text-xs text-gray-500 mt-1">Tipe soal tidak dapat diubah. Hapus dan buat baru jika
                            ingin mengganti tipe.</p>
                    </div>

                    {{-- Upload Gambar Pertanyaan --}}
                    @if ($question->type !== 'HAFALAN')
                        <div class="mb-4">
                            <label for="question_image" class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Gambar Pertanyaan (Opsional)
                            </label>

                            @if ($question->image_path)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Current Image"
                                        class="max-w-sm max-h-64 rounded-lg border border-gray-200 shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">Gambar saat ini. Upload gambar baru untuk
                                        menggantinya.</p>
                                </div>
                            @endif

                            <input type="file" id="question_image" name="question_image" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                        </div>
                    @endif

                    {{-- MATERI HAFALAN (hanya untuk tipe HAFALAN) --}}
                    @if ($question->type === 'HAFALAN')
                        <div class="border border-indigo-200 bg-indigo-50 p-4 rounded-lg mb-4">
                            <h4 class="text-md font-semibold text-indigo-700 mb-3">📚 Materi Hafalan</h4>

                            <div class="mb-3">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten
                                    Memori</label>
                                <textarea id="memory_content" name="memory_content" rows="4" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('memory_content', $question->memory_content) }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe
                                        Konten</label>
                                    <select id="memory_type" name="memory_type" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                        <option value="TEXT"
                                            {{ old('memory_type', $question->memory_type) == 'TEXT' ? 'selected' : '' }}>
                                            Teks</option>
                                        <option value="IMAGE"
                                            {{ old('memory_type', $question->memory_type) == 'IMAGE' ? 'selected' : '' }}>
                                            Gambar</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi
                                        Tampil (Detik)</label>
                                    <input id="duration_seconds" name="duration_seconds" type="number" min="1"
                                        required value="{{ old('duration_seconds', $question->duration_seconds) }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- TEKS PERTANYAAN --}}
                    <div class="mb-4">
                        <label for="question_text" class="block text-sm font-medium text-gray-700">
                            @if ($question->type === 'HAFALAN')
                                ❓ Pertanyaan (setelah hafalan)
                            @else
                                Teks Pertanyaan
                            @endif
                        </label>
                        <textarea id="question_text" name="question_text" rows="3" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('question_text', $question->question_text) }}</textarea>
                    </div>

                    {{-- OPSI JAWABAN (untuk PILIHAN_GANDA, PILIHAN_GANDA_KOMPLEKS, dan HAFALAN) --}}
                    @if (in_array($question->type, ['PILIHAN_GANDA', 'PILIHAN_GANDA_KOMPLEKS', 'HAFALAN']))
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-md font-semibold text-gray-800">Opsi Jawaban</h4>
                                <button type="button" id="addOptionBtn"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                    ➕ Tambah Opsi
                                </button>
                            </div>

                            {{-- Instruction Text --}}
                            <p class="text-sm text-gray-600 mb-3" id="option-instruction">
                                @if ($question->type === 'PILIHAN_GANDA_KOMPLEKS')
                                    ⚠️ Minimal 2 opsi harus diisi. <strong>Centang satu atau lebih checkbox</strong>
                                    untuk jawaban yang benar (bisa lebih dari 1).
                                @else
                                    ⚠️ Minimal 2 opsi harus diisi. Pilih salah satu sebagai jawaban benar dengan
                                    mencentang.
                                @endif
                            </p>

                            <div class="border border-gray-200 p-4 rounded-lg">
                                <div id="optionsList" class="space-y-3">
                                    @php
                                        $opts = is_string($question->options)
                                            ? json_decode($question->options, true)
                                            : $question->options ?? [];
                                        $isKompleks = $question->type === 'PILIHAN_GANDA_KOMPLEKS';

                                        // Parse correct answers
                                        if ($isKompleks) {
                                            $rawCorrect = $question->correct_answer_index;
                                            $correctAnswers = [];

                                            if (is_array($rawCorrect)) {
                                                $correctAnswers = $rawCorrect;
                                            } elseif (is_string($rawCorrect)) {
                                                // Coba decode JSON
                                                $decoded = json_decode($rawCorrect, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $correctAnswers = $decoded;
                                                } else {
                                                    // Fallback: Coba explode koma (misal "0,1") atau single value
                                                    $correctAnswers = explode(',', $rawCorrect);
                                                }
                                            } elseif (!is_null($rawCorrect)) {
                                                $correctAnswers = [$rawCorrect];
                                            }

                                            // Normalisasi ke string untuk perbandingan
                                            $correctAnswers = array_map(function ($item) {
                                                return (string) trim($item);
                                            }, $correctAnswers);
                                        } else {
                                            $correctAnswer = $question->correct_answer_index;
                                        }
                                    @endphp

                                    @forelse($opts as $index => $option)
                                        <div class="option-item bg-gray-50 p-3 rounded-lg border-2 border-gray-200 hover:border-indigo-300 transition"
                                            data-index="{{ $index }}">
                                            <div class="flex items-start space-x-3 w-full">
                                                <div class="flex items-center pt-2">
                                                    @if ($isKompleks)
                                                        {{-- Checkbox untuk multiple answers --}}
                                                        <input type="checkbox" name="correct_answers[]"
                                                            value="{{ $index }}"
                                                            class="input-correct h-4 w-4 text-green-600"
                                                            {{ in_array((string) $index, $correctAnswers) ? 'checked' : '' }}>
                                                    @else
                                                        {{-- Radio untuk single answer --}}
                                                        <input type="radio" name="is_correct"
                                                            value="{{ $index }}"
                                                            class="input-correct h-4 w-4 text-green-600"
                                                            {{ old('is_correct', $correctAnswer) == $index ? 'checked' : '' }}
                                                            required>
                                                    @endif
                                                    <label class="ml-2 text-sm text-gray-600">Benar</label>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="block text-xs font-medium text-gray-500 option-label">
                                                        Opsi {{ chr(65 + $index) }}
                                                    </label>
                                                    <input type="text" name="options[{{ $index }}][text]"
                                                        value="{{ old('options.' . $index . '.text', $option['text'] ?? '') }}"
                                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input"
                                                        placeholder="Masukkan teks untuk Opsi {{ chr(65 + $index) }}">
                                                    <input type="hidden" name="options[{{ $index }}][index]"
                                                        value="{{ $index }}">

                                                    {{-- Gambar Opsi (jika ada) --}}
                                                    @if (isset($option['image_path']) && $option['image_path'])
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $option['image_path']) }}"
                                                                alt="Opsi {{ chr(65 + $index) }}"
                                                                class="max-w-xs max-h-32 rounded border border-gray-200">
                                                            <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                                                        </div>
                                                    @endif

                                                    {{-- Upload Gambar Opsi Baru --}}
                                                    <div class="mt-2">
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                                            Upload Gambar Opsi Baru (Opsional)
                                                        </label>
                                                        <input type="file"
                                                            name="options[{{ $index }}][image_file]"
                                                            accept="image/*"
                                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="remove-option-btn text-red-500 hover:text-red-700 pt-2"
                                                    style="{{ count($opts) <= 2 ? 'display: none;' : '' }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        {{-- Jika tidak ada opsi, buat 4 opsi default --}}
                                        @for ($i = 0; $i < 4; $i++)
                                            <div class="option-item bg-gray-50 p-3 rounded-lg border-2 border-gray-200 hover:border-indigo-300 transition"
                                                data-index="{{ $i }}">
                                                <div class="flex items-start space-x-3 w-full">
                                                    <div class="flex items-center pt-2">
                                                        @if ($isKompleks)
                                                            <input type="checkbox" name="correct_answers[]"
                                                                value="{{ $i }}"
                                                                class="input-correct h-4 w-4 text-green-600"
                                                                {{ $i == 0 ? 'checked' : '' }}>
                                                        @else
                                                            <input type="radio" name="is_correct"
                                                                value="{{ $i }}"
                                                                class="input-correct h-4 w-4 text-green-600"
                                                                {{ $i == 0 ? 'checked' : '' }} required>
                                                        @endif
                                                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                                                    </div>
                                                    <div class="flex-1">
                                                        <label
                                                            class="block text-xs font-medium text-gray-500 option-label">Opsi
                                                            {{ chr(65 + $i) }}</label>
                                                        <input type="text"
                                                            name="options[{{ $i }}][text]"
                                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input">
                                                        <input type="hidden"
                                                            name="options[{{ $i }}][index]"
                                                            value="{{ $i }}">
                                                        <div class="mt-2">
                                                            <label
                                                                class="block text-xs font-medium text-gray-600 mb-1">Upload
                                                                Gambar Opsi (Opsional)</label>
                                                            <input type="file"
                                                                name="options[{{ $i }}][image_file]"
                                                                accept="image/*"
                                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        class="remove-option-btn text-red-500 hover:text-red-700 pt-2"
                                                        style="{{ $i < 2 ? 'display: none;' : '' }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endfor
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex justify-end mt-6 space-x-3 border-t pt-4">
                        <a href="{{ route('admin.alat-tes.questions.index', $alat_te->id) }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');
            const questionType = document.getElementById('questionType').value;
            const editForm = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');
            const isKompleks = questionType === 'PILIHAN_GANDA_KOMPLEKS';

            // Fungsi update label dan index
            function updateOptionLabels() {
                const items = optionsList.querySelectorAll('.option-item');
                items.forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const correctInput = item.querySelector('.input-correct');
                    const removeBtn = item.querySelector('.remove-option-btn');
                    const hiddenIndex = item.querySelector('input[name*="[index]"]');
                    const imageInput = item.querySelector('input[type="file"]');

                    const letter = String.fromCharCode(65 + index);

                    if (label) label.textContent = `Opsi ${letter}`;
                    if (input) {
                        input.name = `options[${index}][text]`;
                        input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                    }
                    if (imageInput) imageInput.name = `options[${index}][image_file]`;
                    if (correctInput) correctInput.value = index;
                    if (hiddenIndex) hiddenIndex.value = index;
                    if (removeBtn) removeBtn.style.display = items.length > 2 ? 'block' : 'none';
                });
            }

            // Tambah opsi baru
            if (addOptionBtn && optionsList) {
                addOptionBtn.addEventListener('click', function() {
                    const newIndex = optionsList.children.length;
                    const letter = String.fromCharCode(65 + newIndex);

                    const inputType = isKompleks ? 'checkbox' : 'radio';
                    const inputName = isKompleks ? 'correct_answers[]' : 'is_correct';
                    const requiredAttr = isKompleks ? '' : 'required';

                    const newOption = document.createElement('div');
                    newOption.className =
                        'option-item bg-gray-50 p-3 rounded-lg border-2 border-gray-200 hover:border-indigo-300 transition';
                    newOption.innerHTML = `
                        <div class="flex items-start space-x-3 w-full">
                            <div class="flex items-center pt-2">
                                <input type="${inputType}" name="${inputName}" value="${newIndex}" 
                                    class="input-correct h-4 w-4 text-green-600" ${requiredAttr}>
                                <label class="ml-2 text-sm text-gray-600">Benar</label>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 option-label">Opsi ${letter}</label>
                                <input type="text" name="options[${newIndex}][text]" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input"
                                    placeholder="Masukkan teks untuk Opsi ${letter}">
                                <input type="hidden" name="options[${newIndex}][index]" value="${newIndex}">
                                <div class="mt-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Upload Gambar Opsi (Opsional)</label>
                                    <input type="file" name="options[${newIndex}][image_file]" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
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
                    updateOptionLabels();
                });
            }

            // Hapus opsi
            if (optionsList) {
                optionsList.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-option-btn');
                    const optionItem = e.target.closest('.option-item');

                    if (removeBtn && optionItem) {
                        const totalOptions = optionsList.children.length;
                        if (totalOptions > 2) {
                            optionItem.remove();
                            updateOptionLabels();
                        } else {
                            alert('Minimal harus ada 2 Opsi Jawaban.');
                        }
                    }
                });
            }

            // Form validation
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    if (questionType === 'PILIHAN_GANDA' || questionType === 'PILIHAN_GANDA_KOMPLEKS') {
                        let hasCorrectAnswer = false;

                        if (isKompleks) {
                            const checkedBoxes = document.querySelectorAll('.input-correct:checked');
                            hasCorrectAnswer = checkedBoxes.length > 0;

                            if (!hasCorrectAnswer) {
                                e.preventDefault();
                                alert(
                                    '⚠️ Anda harus memilih minimal satu jawaban yang benar!\n\nCentang satu atau lebih checkbox.');
                                return false;
                            }
                        } else {
                            hasCorrectAnswer = document.querySelector(
                                'input[name="is_correct"]:checked') !== null;

                            if (!hasCorrectAnswer) {
                                e.preventDefault();
                                alert(
                                    '⚠️ Anda harus memilih satu jawaban yang benar!\n\nCentang salah satu radio button.');
                                return false;
                            }
                        }

                        // Check if at least 2 options are filled
                        const optionTexts = document.querySelectorAll(
                            'input[name^="options"][name$="[text]"]');
                        let filledOptions = 0;
                        optionTexts.forEach(input => {
                            if (input.value.trim() !== '') {
                                filledOptions++;
                            }
                        });

                        if (filledOptions < 2) {
                            e.preventDefault();
                            alert('⚠️ Minimal harus ada 2 opsi jawaban yang diisi!');
                            return false;
                        }
                    }

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '⏳ Menyimpan...';
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            }

            // Initial update
            updateOptionLabels();
        });
    </script>
</x-admin-layout>
