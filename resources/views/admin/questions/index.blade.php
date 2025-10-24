<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Soal untuk Alat Tes: ') }}{{ $AlatTes->name }}
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
                    <button type="button" id="toggleFormBtn"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                        {{ $errors->any() ? 'Sembunyikan Form' : 'Tambah Soal' }}
                    </button>
                </div>

                <div id="formContainer" style="display: {{ $errors->any() ? 'block' : 'none' }};">
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
                        action="{{ route('admin.alat-tes.questions.store', ['alat_te' => $AlatTes->id]) }}"
                        id="questionForm" enctype="multipart/form-data">
                        @csrf

                        {{-- Tipe Pertanyaan --}}
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe
                                Pertanyaan</label>
                            <select id="type" name="type" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="PILIHAN_GANDA"
                                    {{ old('type', 'PILIHAN_GANDA') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan
                                    Ganda</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai</option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan
                                </option>
                                <option value="PAPIKOSTICK" {{ old('type') == 'PAPIKOSTICK' ? 'selected' : '' }}>PAPI
                                    KOSTICK (Pasangan Pernyataan)</option>
                            </select>
                        </div>

                        {{-- Input Khusus PAPI KOSTICK (Hanya Item Number) --}}
                        {{-- Blok ini disederhanakan dan semua input role_a, need_a, role_b, need_b DIHAPUS --}}
                        <div id="papi-scoring-container"
                            class="border border-red-200 bg-red-50 p-4 rounded-lg mb-4 hidden">
                            <h4 class="text-md font-semibold text-red-700 mb-3">⚙️ Pengaturan PAPI KOSTICK</h4>

                            <div class="mb-4">
                                <label for="papi_item_number" class="block text-sm font-medium text-gray-700">Nomor Soal
                                    PAPI (1-90)</label>
                                <input id="papi_item_number" name="papi_item_number" type="number" min="1"
                                    max="90" value="{{ old('papi_item_number') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <p class="text-xs text-red-500 mt-1">CATATAN: Input Role dan Need telah dihapus. Harap
                                    gunakan import Excel untuk mengisi aspek skor secara massal.</p>
                            </div>
                        </div>

                        {{-- Upload Gambar Pertanyaan --}}
                        <div class="mb-4" id="question-image-container">
                            <label for="question_image" class="block text-sm font-medium text-gray-700 mb-1">
                                Upload Gambar Pertanyaan (Opsional)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="file" id="question_image" name="question_image" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="button" id="removeImageBtn" style="display: none;"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Hapus
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>

                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview"
                                    class="max-w-xs rounded-lg border border-gray-300">
                            </div>

                            @error('question_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- MATERI HAFALAN CONTAINER --}}
                        <div id="memory-container"
                            class="border border-indigo-200 bg-indigo-50 p-4 rounded-lg mb-4 hidden">
                            <h4 class="text-md font-semibold text-indigo-700 mb-3">📚 Materi Hafalan</h4>
                            <p class="text-sm text-gray-600 mb-3">Materi ini akan ditampilkan selama beberapa detik,
                                kemudian siswa akan menjawab pertanyaan di bawah.</p>

                            <div class="mb-3">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten
                                    Memori</label>
                                <textarea id="memory_content" name="memory_content" rows="4"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                    placeholder="Contoh: BUNGA - Dahlia, Flamboyan, Laret, Soka, Yasmin&#10;PERKAKAS - Cangkul, Jarum, Kikir, Palu, Wajan">{{ old('memory_content') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Masukkan materi yang harus dihafal siswa</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe
                                        Konten</label>
                                    <select id="memory_type" name="memory_type"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                        <option value="TEXT"
                                            {{ old('memory_type', 'TEXT') == 'TEXT' ? 'selected' : '' }}>Teks</option>
                                        <option value="IMAGE" {{ old('memory_type') == 'IMAGE' ? 'selected' : '' }}>
                                            Gambar</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi
                                        Tampil (Detik)</label>
                                    <input id="duration_seconds" name="duration_seconds" type="number" min="1"
                                        value="{{ old('duration_seconds', 10) }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">Berapa lama materi ditampilkan</p>
                                </div>
                            </div>
                        </div>

                        {{-- TEKS PERTANYAAN (setelah hafalan) --}}
                        <div id="question-text-container" class="mb-4">
                            <label for="question_text" class="block text-sm font-medium text-gray-700">
                                <span id="question-text-label">Teks Pertanyaan</span>
                            </label>
                            <textarea id="question_text" name="question_text" rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1" id="question-text-hint">Pertanyaan untuk siswa</p>
                        </div>

                        {{-- OPSI JAWABAN --}}
                        <div id="options-section" class="mb-4">
                            <div class="mb-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800">Opsi Jawaban</h4>
                                        <p class="text-xs text-gray-500" id="options-hint">Pilihan jawaban untuk
                                            pertanyaan</p>
                                    </div>
                                    <button type="button" id="addOptionBtn"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                                        ➕ Tambah Opsi
                                    </button>
                                </div>
                            </div>

                            <div id="options-container" class="border border-gray-200 p-4 rounded-lg">
                                <div id="optionsList" class="space-y-3">
                                    {{-- Opsi A, B, C, D default (4 opsi) --}}
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="option-item bg-gray-50 p-3 rounded-lg"
                                            data-index="{{ $i }}">
                                            <div class="flex items-start space-x-3 w-full">
                                                <div class="flex items-start space-x-3 w-full">
                                                    <div class="flex items-center pt-2 correct-radio-block">
                                                        <input type="radio" name="is_correct"
                                                            value="{{ $i }}"
                                                            class="h-4 w-4 text-green-600 correct-radio"
                                                            {{ old('is_correct') == $i ? 'checked' : '' }}>
                                                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                                                    </div>
                                                    <div class="flex-1">
                                                        <label
                                                            class="block text-xs font-medium text-gray-500 option-label">Opsi
                                                            {{ chr(65 + $i) }}</label>
                                                        <input type="text"
                                                            name="options[{{ $i }}][text]"
                                                            value="{{ old('options.' . $i . '.text') }}"
                                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm option-input"
                                                            placeholder="Masukkan teks untuk Opsi {{ chr(65 + $i) }}">
                                                        <input type="hidden"
                                                            name="options[{{ $i }}][index]"
                                                            value="{{ $i }}">

                                                        {{-- Upload Gambar untuk Opsi --}}
                                                        <div class="mt-3">
                                                            <label
                                                                class="block text-xs font-medium text-gray-600 mb-1">Upload
                                                                Gambar Opsi (Opsional)</label>
                                                            <input type="file"
                                                                name="options[{{ $i }}][image_file]"
                                                                accept="image/*"
                                                                class="option-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                            <div class="option-image-preview mt-2"
                                                                style="display: none;">
                                                                <img src="" alt="Preview Opsi"
                                                                    class="max-w-xs max-h-32 rounded border border-gray-300 option-preview-img">
                                                                <button type="button"
                                                                    class="remove-option-image-btn text-red-600 hover:text-red-800 text-xs font-medium mt-1">
                                                                    Hapus Gambar
                                                                </button>
                                                            </div>
                                                        </div>
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
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelBtn"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            ---

            {{-- Daftar Soal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Soal</h3>

                @if ($questions->count() > 0)
                    <div class="space-y-4">
                        @foreach ($questions as $index => $question)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                                {{ $question->type }}
                                            </span>
                                            <span class="text-gray-500 text-sm">Soal
                                                #{{ $questions->firstItem() + $index }}</span>

                                            @if ($question->type == 'HAFALAN')
                                                <span
                                                    class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">
                                                    ⏱️ {{ $question->duration_seconds }}s
                                                </span>
                                            @endif

                                            {{-- Tampilkan Nomor PAPI jika ada --}}
                                            @if ($question->type == 'PAPIKOSTICK' && $question->item_number)
                                                <span
                                                    class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                                                    Item PAPI: {{ $question->item_number }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Tampilkan Materi Hafalan --}}
                                        @if ($question->type == 'HAFALAN' && $question->memory_content)
                                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 mb-3">
                                                <p class="text-xs font-semibold text-indigo-700 mb-1">📚 Materi Hafalan
                                                    ({{ $question->memory_type }})
                                                    :</p>
                                                <p class="text-sm text-gray-700 whitespace-pre-line">
                                                    {{ $question->memory_content }}</p>
                                            </div>
                                        @endif

                                        <p class="text-gray-800 font-medium mb-2">
                                            {{ $question->question_text ?: 'Materi Hafalan' }}
                                        </p>

                                        {{-- Tampilkan Gambar jika ada --}}
                                        @if ($question->image_path)
                                            <div class="my-3">
                                                <img src="{{ asset('storage/' . $question->image_path) }}"
                                                    alt="Question Image"
                                                    class="max-w-sm max-h-64 rounded-lg border border-gray-200 shadow-sm object-contain">
                                            </div>
                                        @endif

                                        @if (
                                            ($question->type == 'PILIHAN_GANDA' || $question->type == 'HAFALAN' || $question->type == 'PAPIKOSTICK') &&
                                                $question->options)
                                            @php
                                                $opts = is_string($question->options)
                                                    ? json_decode($question->options, true)
                                                    : $question->options;
                                            @endphp
                                            <div class="ml-4 space-y-2 text-sm text-gray-600">
                                                @foreach ($opts as $key => $opt)
                                                    <div class="flex items-start">
                                                        <span class="font-semibold mr-2">{{ chr(65 + $key) }}.</span>
                                                        <div class="flex-1">
                                                            <span>{{ $opt['text'] ?? '' }}</span>
                                                            @if (isset($opt['image_path']) && $opt['image_path'])
                                                                <div class="mt-1">
                                                                    <img src="{{ asset('storage/' . $opt['image_path']) }}"
                                                                        alt="Opsi {{ chr(65 + $key) }}"
                                                                        class="max-w-xs max-h-32 rounded border border-gray-200">
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if ($question->correct_answer_index == $key && $question->type != 'PAPIKOSTICK')
                                                            <span class="ml-2 text-green-600 text-xs">✓ Benar</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- KODE PAPI SCORING KEY DIHAPUS KARENA TIDAK ADA LAGI INPUT ROLE/NEED --}}
                                        {{-- Blok ini DIHAPUS:
                                @if ($question->type == 'PAPIKOSTICK')
                                    <div class="mt-3 text-xs bg-red-50 p-2 rounded-lg border border-red-200">
                                        <p class="font-semibold text-red-700">PAPI Scoring Key:</p>
                                        <p class="text-gray-700">A: Role {{ $question->role_a ?? '-' }} / Need {{ $question->need_a ?? '-' }}</p>
                                        <p class="text-gray-700">B: Role {{ $question->role_b ?? '-' }} / Need {{ $question->need_b ?? '-' }}</p>
                                    </div>
                                @endif
                                --}}
                                    </div>

                                    <div class="flex space-x-2 ml-4">
                                        <a href="{{ route('admin.questions.edit', $question->id) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                            Edit
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.questions.destroy', $question->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-medium">
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
        document.addEventListener('DOMContentLoaded', function() {

            const toggleFormBtn = document.getElementById('toggleFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const formContainer = document.getElementById('formContainer');
            const typeSelect = document.getElementById('type');
            const optionsSection = document.getElementById('options-section');
            const memoryContainer = document.getElementById('memory-container');
            const papiContainer = document.getElementById('papi-scoring-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionImageContainer = document.getElementById('question-image-container');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');

            const questionImage = document.getElementById('question_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');

            let optionCount = 4;
            if (optionsList) {
                optionCount = optionsList.children.length;
            }

            // [LOGIC IMAGE PREVIEW - START]
            const setupImagePreviewLogic = (imageInput, previewImg, imagePreview, removeBtn) => {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            imagePreview.style.display = 'block';
                            if (removeBtn) removeBtn.style.display = 'inline-block';
                        }
                        reader.readAsDataURL(file);
                    }
                });

                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        imageInput.value = '';
                        imagePreview.style.display = 'none';
                        removeBtn.style.display = 'none';
                    });
                }
            };

            // Setup main question image preview
            setupImagePreviewLogic(questionImage, previewImg, imagePreview, removeImageBtn);

            // Handle option image preview setup
            function setupOptionImagePreview(optionItem) {
                const imageInput = optionItem.querySelector('.option-image-input');
                const previewContainer = optionItem.querySelector('.option-image-preview');
                const previewImg = optionItem.querySelector('.option-preview-img');
                const removeBtn = optionItem.querySelector('.remove-option-image-btn');

                setupImagePreviewLogic(imageInput, previewImg, previewContainer, removeBtn);
            }

            // Setup initial option image previews
            document.querySelectorAll('.option-item').forEach(item => {
                setupOptionImagePreview(item);
            });
            // [LOGIC IMAGE PREVIEW - END]

            // [LOGIC FORM TOGGLE]
            toggleFormBtn.addEventListener('click', function() {
                const isHidden = formContainer.style.display === 'none';
                formContainer.style.display = isHidden ? 'block' : 'none';
                toggleFormBtn.textContent = isHidden ? 'Sembunyikan Form' : 'Tambah Soal';
                if (isHidden) toggleContainers();
            });

            cancelBtn.addEventListener('click', function() {
                formContainer.style.display = 'none';
                toggleFormBtn.textContent = 'Tambah Soal';
            });

            // [LOGIC UPDATE LABELS & BUTTONS]
            function updateOptionLabels() {
                document.querySelectorAll('.option-item').forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const radio = item.querySelector('.correct-radio');
                    const removeBtn = item.querySelector('.remove-option-btn');
                    const imageInput = item.querySelector('.option-image-input');
                    const hiddenIndex = item.querySelector('input[name*="[index]"]');

                    const letter = String.fromCharCode(65 + index);

                    // Update names and placeholders
                    label.textContent = `Opsi ${letter}`;
                    input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                    input.name = `options[${index}][text]`;
                    imageInput.name = `options[${index}][image_file]`;
                    radio.value = index;
                    if (hiddenIndex) hiddenIndex.value = index;

                    // Atur tombol hapus (Hanya jika bukan PAPI dan lebih dari 2 opsi)
                    if (removeBtn) {
                        removeBtn.style.display = optionCount > 2 && typeSelect.value !== 'PAPIKOSTICK' ?
                            'block' : 'none';
                    }
                });
            }

            // [LOGIC CONTAINER TOGGLE]
            function toggleContainers() {
                const selectedType = typeSelect.value;

                // 1. Reset/Sembunyikan Semua Default
                optionsSection.style.display = 'none';
                memoryContainer.classList.add('hidden');
                papiContainer.classList.add('hidden');
                questionTextContainer.style.display = 'block';
                questionImageContainer.style.display = 'block';
                addOptionBtn.style.display = 'block';

                // Reset Labels
                document.getElementById('question-text-label').textContent = 'Teks Pertanyaan';
                document.getElementById('question-text-hint').textContent = 'Pertanyaan untuk siswa';
                document.getElementById('options-hint').textContent = 'Pilihan jawaban untuk pertanyaan';

                document.querySelectorAll('.option-item').forEach((item, index) => {
                    item.style.display = 'block';
                    const radioBlock = item.querySelector('.correct-radio-block');
                    const removeBtn = item.querySelector('.remove-option-btn');

                    if (radioBlock) radioBlock.style.display = 'flex';
                    if (removeBtn) removeBtn.style.display = index >= 2 ? 'block' : 'none';
                });

                // 2. Tampilkan Kontainer Berdasarkan Tipe
                if (selectedType === 'PAPIKOSTICK') {
                    papiContainer.classList.remove('hidden');
                    optionsSection.style.display = 'block';
                    questionImageContainer.style.display = 'none';

                    // Ganti Label untuk PAPI
                    document.getElementById('question-text-label').textContent = 'Nomor Soal PAPI (1-90)';
                    document.getElementById('question_text').placeholder = 'Masukkan Nomor Soal (mis: 45) di sini.';
                    document.getElementById('question-text-hint').textContent =
                        'Kolom ini hanya untuk Nomor Soal PAPI. Teks pernyataan diisi di Opsi A dan B.';

                    // Atur Opsi Jawaban untuk PAPI (Hanya A dan B)
                    addOptionBtn.style.display = 'none';
                    document.getElementById('options-hint').textContent =
                        'Hanya Opsi A dan B yang digunakan untuk Pasangan Pernyataan PAPI.';

                    document.querySelectorAll('.option-item').forEach((item, index) => {
                        const radioBlock = item.querySelector('.correct-radio-block');

                        if (index >= 2) {
                            item.style.display = 'none';
                        } else {
                            if (radioBlock) radioBlock.style.display = 'none'; // Sembunyikan "Benar"
                        }
                    });

                } else if (selectedType === 'ESSAY') {
                    optionsSection.style.display = 'none';
                    addOptionBtn.style.display = 'none';

                } else if (selectedType === 'HAFALAN') {
                    memoryContainer.classList.remove('hidden');
                    optionsSection.style.display = 'block';
                    questionImageContainer.style.display = 'none';

                    document.getElementById('question-text-label').textContent = '❓ Pertanyaan (setelah hafalan)';
                    document.getElementById('question-text-hint').textContent =
                        'Pertanyaan yang akan muncul setelah materi hafalan hilang';
                }
            }

            // [LOGIC TAMBAH OPSI]
            addOptionBtn.addEventListener('click', function() {
                const newIndex = optionsList.children.length;
                const letter = String.fromCharCode(65 + newIndex);

                const newOption = document.createElement('div');
                newOption.className = 'option-item bg-gray-50 p-3 rounded-lg';
                newOption.innerHTML = `
                    <div class="flex items-start space-x-3 w-full">
                        <div class="flex items-start space-x-3 w-full">
                            <div class="flex items-center pt-2 correct-radio-block">
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
                optionCount = optionsList.children.length; // Update count
                updateOptionLabels();
            });

            // [LOGIC HAPUS OPSI]
            optionsList.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-option-btn');
                const optionItem = e.target.closest('.option-item');

                if (removeBtn && optionItem && optionsList.children.length > 2) {
                    optionItem.remove();
                    optionCount = optionsList.children.length; // Update count
                    updateOptionLabels();
                } else if (removeBtn) {
                    alert('Minimal harus ada 2 Opsi Jawaban.');
                }
            });


            typeSelect.addEventListener('change', toggleContainers);
            toggleContainers();

            @if ($errors->any())
                toggleContainers();
            @endif
        });
    </script>
</x-admin-layout>
