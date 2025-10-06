<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pertanyaan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                <div class="bg-white p-6 rounded-xl">
                    <form method="POST"
                        action="{{ route('admin.alat-tes.questions.store', ['alat_te' => $alatTeId]) }}"
                        id="questionForm">
                        @csrf

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe
                                Pertanyaan</label>
                            <select id="type" name="type" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="PILIHAN_GANDA"
                                    {{ old('type', 'PILIHAN_GANDA') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan
                                    Ganda</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai (Hanya Teks)
                                </option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan (Materi
                                    Memori)</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="question-text-container" class="mb-6">
                            <label for="question_text" class="block text-sm font-medium text-gray-700">Teks
                                Pertanyaan</label>
                            <textarea id="question_text" name="question_text" rows="4"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="options-container" class="border border-gray-200 p-4 rounded-lg mb-6">
                            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-300">
                                <h3 class="text-lg font-semibold text-gray-800">Opsi Jawaban</h3>
                                <button type="button" id="addOptionBtn"
                                    style="background: green; color: white; padding: 8px 16px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                                    + Tambah Opsi
                                </button>
                            </div>

                            <div id="optionsList" class="space-y-3">
                                @php
                                    $oldOptions = old('options', []);
                                    $oldIsCorrect = old('is_correct');
                                    $defaultOptions = empty($oldOptions) ? 4 : count($oldOptions);
                                @endphp

                                @for ($i = 0; $i < $defaultOptions; $i++)
                                    <div class="option-item flex items-start space-x-3 bg-gray-50 p-3 rounded-lg"
                                        data-index="{{ $i }}">
                                        <div class="flex items-center pt-2">
                                            <input type="radio" name="is_correct" value="{{ $i }}"
                                                {{ $oldIsCorrect == $i ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500 correct-radio">
                                            <label class="ml-2 text-sm text-gray-600">Benar</label>
                                        </div>

                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-500 option-label">Opsi
                                                {{ chr(65 + $i) }}</label>
                                            <input type="text" name="options[{{ $i }}][text]"
                                                value="{{ $oldOptions[$i]['text'] ?? '' }}"
                                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 option-input"
                                                placeholder="Masukkan teks untuk Opsi {{ chr(65 + $i) }}">
                                            <input type="hidden" name="options[{{ $i }}][index]"
                                                value="{{ $i }}">
                                        </div>

                                        <button type="button"
                                            class="remove-option-btn text-red-500 hover:text-red-700 pt-2"
                                            style="{{ $i < 2 ? 'display: none;' : '' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endfor
                            </div>

                            @error('options')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('is_correct')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="memory-container"
                            class="border border-indigo-200 p-4 rounded-lg space-y-4 mb-6 hidden">
                            <h3 class="text-lg font-semibold text-indigo-700 border-b pb-2">Materi Hafalan</h3>

                            <div class="mb-4">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten
                                    Memori (Teks/URL Gambar)</label>
                                <textarea id="memory_content" name="memory_content" rows="4"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                    placeholder="Masukkan teks atau URL gambar yang harus dihafal.">{{ old('memory_content') }}</textarea>
                                @error('memory_content')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe
                                    Konten</label>
                                <select id="memory_type" name="memory_type"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="TEXT"
                                        {{ old('memory_type', 'TEXT') == 'TEXT' ? 'selected' : '' }}>Teks</option>
                                    <option value="IMAGE" {{ old('memory_type') == 'IMAGE' ? 'selected' : '' }}>Gambar
                                        (Harap masukkan URL/path)</option>
                                </select>
                                @error('memory_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi
                                    Tampil (Detik)</label>
                                <input id="duration_seconds" name="duration_seconds" type="number" min="1"
                                    value="{{ old('duration_seconds', 10) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                @error('duration_seconds')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <p class="text-sm text-indigo-600 pt-2">Setelah item memori ini dibuat, Anda akan
                                menambahkan *soal recall* (Pilihan Ganda) yang merujuk padanya.</p>
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <a href="{{ route('admin.alat-tes.questions.index', $alatTeId) }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md">
                                Batal
                            </a>
                            <button type="submit" id="submitBtn"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const optionsContainer = document.getElementById('options-container');
            const memoryContainer = document.getElementById('memory-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionTextarea = document.getElementById('question_text');
            const form = document.getElementById('questionForm');
            const submitBtn = document.getElementById('submitBtn');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');

            let optionCount = document.querySelectorAll('.option-item').length;

            function toggleContainers() {
                const selectedType = typeSelect.value;

                optionsContainer.classList.add('hidden');
                memoryContainer.classList.add('hidden');
                questionTextContainer.classList.add('hidden');

                questionTextarea.required = false;
                document.getElementById('memory_content').required = false;
                updateOptionInputsRequired(false);

                if (selectedType === 'PILIHAN_GANDA' || selectedType === 'ESSAY') {
                    questionTextContainer.classList.remove('hidden');
                    questionTextarea.required = true;
                }

                if (selectedType === 'PILIHAN_GANDA') {
                    optionsContainer.classList.remove('hidden');
                    updateOptionInputsRequired(true);
                } else if (selectedType === 'HAFALAN') {
                    memoryContainer.classList.remove('hidden');
                    document.getElementById('memory_content').required = true;
                }
            }

            function updateOptionInputsRequired(required) {
                document.querySelectorAll('.option-input').forEach(input => {
                    input.required = required;
                });
            }

            function updateOptionLabels() {
                document.querySelectorAll('.option-item').forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const radio = item.querySelector('.correct-radio');
                    const hiddenIndex = item.querySelector('input[type="hidden"]');
                    const removeBtn = item.querySelector('.remove-option-btn');

                    const letter = String.fromCharCode(65 + index);
                    label.textContent = `Opsi ${letter}`;
                    input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                    input.name = `options[${index}][text]`;
                    radio.value = index;
                    hiddenIndex.value = index;
                    item.dataset.index = index;

                    removeBtn.style.display = optionCount > 2 ? 'block' : 'none';
                });
            }

            addOptionBtn.addEventListener('click', function() {
                const newIndex = optionCount;
                const letter = String.fromCharCode(65 + newIndex);

                const newOption = document.createElement('div');
                newOption.className = 'option-item flex items-start space-x-3 bg-gray-50 p-3 rounded-lg';
                newOption.dataset.index = newIndex;
                newOption.innerHTML = `
                    <div class="flex items-center pt-2">
                        <input type="radio" 
                               name="is_correct" 
                               value="${newIndex}" 
                               class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500 correct-radio">
                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                    </div>
                    
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 option-label">Opsi ${letter}</label>
                        <input type="text" 
                               name="options[${newIndex}][text]" 
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 option-input" 
                               placeholder="Masukkan teks untuk Opsi ${letter}">
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

                if (typeSelect.value === 'PILIHAN_GANDA') {
                    updateOptionInputsRequired(true);
                }
            });

            optionsList.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-option-btn');
                if (removeBtn && optionCount > 2) {
                    const optionItem = removeBtn.closest('.option-item');
                    optionItem.remove();
                    optionCount--;
                    updateOptionLabels();
                }
            });

            toggleContainers();
            typeSelect.addEventListener('change', toggleContainers);

            form.addEventListener('submit', function(e) {
                const selectedType = typeSelect.value;

                if (selectedType === 'PILIHAN_GANDA') {
                    const isCorrectChecked = document.querySelector('input[name="is_correct"]:checked');

                    if (!isCorrectChecked) {
                        e.preventDefault();
                        alert('Harap pilih salah satu opsi sebagai jawaban yang benar!');
                        return false;
                    }

                    let allOptionsFilled = true;
                    document.querySelectorAll('.option-input').forEach(input => {
                        if (!input.value.trim()) {
                            allOptionsFilled = false;
                        }
                    });

                    if (!allOptionsFilled) {
                        e.preventDefault();
                        alert('Harap isi semua opsi jawaban!');
                        return false;
                    }
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Menyimpan...';
            });
        });
    </script>
</x-admin-layout>
