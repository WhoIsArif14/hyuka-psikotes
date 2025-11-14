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

                        {{-- Input Khusus PAPI KOSTICK --}}
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

                        {{-- TAB NAVIGATION --}}
                        <div class="mb-6 border-b border-gray-200">
                            <nav class="flex space-x-4" id="tabNav">
                                <button type="button" 
                                        class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-blue-600 text-blue-600" 
                                        data-tab="soal">
                                    📝 Soal Utama
                                </button>
                                <button type="button" 
                                        class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" 
                                        data-tab="contoh">
                                    📚 Contoh Soal
                                </button>
                                <button type="button" 
                                        class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700" 
                                        data-tab="instruksi">
                                    ❓ Instruksi
                                </button>
                            </nav>
                        </div>

                        {{-- TAB CONTENT --}}
                        <div class="tab-contents">
                            {{-- TAB SOAL UTAMA --}}
                            <div class="tab-content" id="tab-soal">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-blue-900 mb-1">Soal yang akan dijawab peserta</h3>
                                            <p class="text-sm text-blue-700">Ini adalah soal aktual yang akan diberikan kepada peserta dalam tes.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- MATERI HAFALAN CONTAINER --}}
                                <div id="memory-container"
                                    class="border border-indigo-200 bg-indigo-50 p-4 rounded-lg mb-4 hidden">
                                    <h4 class="text-md font-semibold text-indigo-700 mb-3">📚 Materi Hafalan</h4>
                                    <p class="text-sm text-gray-600 mb-3">Materi ini akan ditampilkan selama beberapa detik,
                                        kemudian peserta akan menjawab pertanyaan di bawah.</p>

                                    <div class="mb-3">
                                        <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten
                                            Memori</label>
                                        <textarea id="memory_content" name="memory_content" rows="4"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                            placeholder="Contoh: BUNGA - Dahlia, Flamboyan, Laret, Soka, Yasmin&#10;PERKAKAS - Cangkul, Jarum, Kikir, Palu, Wajan">{{ old('memory_content') }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">Masukkan materi yang harus dihafal peserta</p>
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

                                {{-- Upload Gambar Pertanyaan --}}
                                <div class="mb-4" id="question-image-container">
                                    <label for="question_image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Upload Gambar Pertanyaan (Opsional)
                                    </label>
                                    
                                    {{-- ✅ WARNING BOX --}}
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 mb-2 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-xs text-yellow-800">
                                            <strong>Perhatian:</strong> Ukuran file maksimal <strong class="text-red-600">5 MB</strong>. File yang lebih besar akan ditolak.
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <input type="file" id="question_image" name="question_image" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <button type="button" id="removeImageBtn" style="display: none;"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Hapus
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. <span class="text-red-600 font-semibold">Maksimal 5MB</span></p>

                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview"
                                            class="max-w-xs rounded-lg border border-gray-300">
                                    </div>

                                    @error('question_image')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- TEKS PERTANYAAN --}}
                                <div id="question-text-container" class="mb-4">
                                    <label for="question_text" class="block text-sm font-medium text-gray-700">
                                        <span id="question-text-label">Teks Pertanyaan</span>
                                    </label>
                                    <textarea id="question_text" name="question_text" rows="3"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1" id="question-text-hint">Pertanyaan untuk peserta</p>
                                </div>
                            </div>

                            {{-- TAB CONTOH SOAL --}}
                            <div class="tab-content hidden" id="tab-contoh">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-green-900 mb-1">Contoh untuk pemahaman peserta</h3>
                                            <p class="text-sm text-green-700">Berikan contoh soal serupa dengan jawabannya untuk membantu peserta memahami format tes.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="example_question" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contoh Soal & Pembahasan (Opsional)
                                    </label>
                                    <textarea 
                                        id="example_question" 
                                        name="example_question" 
                                        rows="12"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                        placeholder="Contoh:&#10;&#10;Soal: Jika 2 + 3 = 5, maka 4 + 5 = ?&#10;&#10;Pilihan:&#10;A. 7&#10;B. 8&#10;C. 9&#10;D. 10&#10;&#10;Jawaban: C (9)&#10;&#10;Pembahasan: &#10;Kita tinggal menjumlahkan kedua angka, 4 + 5 = 9. Pola yang sama dengan contoh di soal awal.">{{ old('example_question') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-2">💡 Berikan contoh lengkap dengan jawaban dan pembahasannya agar peserta paham format soal</p>
                                </div>
                            </div>

                            {{-- TAB INSTRUKSI --}}
                            <div class="tab-content hidden" id="tab-instruksi">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-purple-900 mb-1">Panduan cara mengerjakan</h3>
                                            <p class="text-sm text-purple-700">Jelaskan cara mengerjakan soal, tips, dan hal-hal yang perlu diperhatikan peserta.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Instruksi & Cara Menjawab (Opsional)
                                    </label>
                                    <textarea 
                                        id="instructions" 
                                        name="instructions" 
                                        rows="10"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Contoh:&#10;&#10;Cara mengerjakan:&#10;1. Baca soal dengan teliti dan pahami apa yang ditanyakan&#10;2. Perhatikan kata kunci dalam soal&#10;3. Analisis setiap pilihan jawaban dengan hati-hati&#10;4. Pilih jawaban yang paling tepat&#10;5. Periksa kembali jawaban Anda sebelum melanjutkan&#10;&#10;Tips penting:&#10;• Kerjakan soal yang mudah terlebih dahulu&#10;• Jangan terlalu lama di satu soal (maks 2 menit)&#10;• Eliminasi jawaban yang jelas salah&#10;• Jika ragu, gunakan logika dan intuisi Anda&#10;&#10;Perhatian:&#10;⚠️ Jawaban tidak bisa diubah setelah diklik&#10;⚠️ Pastikan pilihan Anda sudah benar">{{ old('instructions') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-2">💡 Berikan instruksi yang jelas dan mudah dipahami untuk membantu peserta</p>
                                </div>
                            </div>
                        </div>

                        {{-- PREVIEW INFO --}}
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Urutan Tampilan untuk peserta
                            </h4>
                            <div class="space-y-2 text-sm text-gray-700 ml-7">
                                <p><span class="font-semibold">1. Instruksi</span> → Ditampilkan di awal sebelum peserta mulai tes</p>
                                <p><span class="font-semibold">2. Contoh Soal</span> → Ditampilkan untuk pemahaman format soal</p>
                                <p><span class="font-semibold">3. Soal Utama</span> → Soal yang akan dijawab oleh peserta</p>
                            </div>
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
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Upload Gambar Opsi (Opsional)</label>
                                                            
                                                            {{-- ✅ WARNING BOX UNTUK OPSI --}}
                                                            <div class="bg-yellow-50 border border-yellow-200 rounded p-1.5 mb-2 flex items-start gap-1.5">
                                                                <svg class="w-3 h-3 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <p class="text-xs text-yellow-800">Maks <strong class="text-red-600">5 MB</strong></p>
                                                            </div>
                                                            
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

            {{-- ✅ TAMPILKAN SOAL PAPI (jika ada) --}}
            @if ($papiQuestions->count() > 0)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-red-700 mb-3">📋 Soal PAPI Kostick</h4>
                    <div class="space-y-4">
                        @foreach ($papiQuestions as $papiQuestion)
                            <div class="border border-red-200 rounded-lg p-4 hover:shadow-md transition bg-red-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                                                PAPI KOSTICK
                                            </span>
                                            <span
                                                class="bg-red-200 text-red-900 text-xs font-semibold px-2 py-1 rounded">
                                                Item #{{ $papiQuestion->item_number }}
                                            </span>
                                        </div>

                                        {{-- Pernyataan A dan B --}}
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-start">
                                                <span class="font-semibold mr-2 text-gray-700">A.</span>
                                                <span class="text-gray-800">{{ $papiQuestion->statement_a }}</span>
                                            </div>
                                            <div class="flex items-start">
                                                <span class="font-semibold mr-2 text-gray-700">B.</span>
                                                <span class="text-gray-800">{{ $papiQuestion->statement_b }}</span>
                                            </div>
                                        </div>

                                        {{-- Scoring Key (jika sudah diisi) --}}
                                        @if ($papiQuestion->role_a || $papiQuestion->need_a || $papiQuestion->role_b || $papiQuestion->need_b)
                                            <div class="mt-3 text-xs bg-red-100 p-2 rounded border border-red-300">
                                                <p class="font-semibold text-red-800 mb-1">Scoring Key:</p>
                                                <div class="grid grid-cols-2 gap-2 text-gray-700">
                                                    <div>A: Role {{ $papiQuestion->role_a ?? '-' }} / Need
                                                        {{ $papiQuestion->need_a ?? '-' }}</div>
                                                    <div>B: Role {{ $papiQuestion->role_b ?? '-' }} / Need
                                                        {{ $papiQuestion->need_b ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ✅ PERBAIKAN: Gunakan route yang BENAR --}}
                                    <div class="flex space-x-2 ml-4">
                                        <a href="{{ route('admin.alat-tes.questions.edit', ['alat_te' => $AlatTes->id, 'question' => $papiQuestion->id]) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Edit
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.questions.destroy', $papiQuestion->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus soal PAPI Item {{ $papiQuestion->item_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination PAPI --}}
                    <div class="mt-4">
                        {{ $papiQuestions->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ TAMPILKAN SOAL UMUM (jika ada) --}}
            @if ($questions->count() > 0)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-3">📝 Soal Umum</h4>
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
                                        </div>

                                        {{-- Tampilkan Instruksi --}}
                                        @if ($question->instructions)
                                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-3">
                                                <p class="text-xs font-semibold text-purple-700 mb-1">❓ Instruksi:</p>
                                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ Str::limit($question->instructions, 150) }}</p>
                                            </div>
                                        @endif

                                        {{-- Tampilkan Contoh Soal --}}
                                        @if ($question->example_question)
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                                <p class="text-xs font-semibold text-green-700 mb-1">📚 Contoh Soal:</p>
                                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ Str::limit($question->example_question, 150) }}</p>
                                            </div>
                                        @endif

                                        {{-- Tampilkan Materi Hafalan --}}
                                        @if ($question->type == 'HAFALAN' && $question->memory_content)
                                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 mb-3">
                                                <p class="text-xs font-semibold text-indigo-700 mb-1">📚 Materi Hafalan
                                                    ({{ $question->memory_type }}):</p>
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

                                        @if (($question->type == 'PILIHAN_GANDA' || $question->type == 'HAFALAN') && $question->options)
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
                                                        @if ($question->correct_answer_index == $key)
                                                            <span class="ml-2 text-green-600 text-xs">✓ Benar</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ✅ SUDAH BENAR - Soal Umum --}}
                                    <div class="flex space-x-2 ml-4">
                                        <a href="{{ route('admin.alat-tes.questions.edit', ['alat_te' => $AlatTes->id, 'question' => $question->id]) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Edit
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.questions.destroy', $question->id) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination Soal Umum --}}
                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ JIKA TIDAK ADA SOAL SAMA SEKALI --}}
            @if ($questions->count() == 0 && $papiQuestions->count() == 0)
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada soal. Klik "Tambah Soal" untuk membuat soal pertama.</p>
                </div>
            @endif

        </div>
    </div>

    <style>
        /* Tab Styles */
        .tab-btn {
            transition: all 0.3s ease;
        }
        .tab-btn:hover {
            border-bottom-color: #9CA3AF;
        }
        .tab-btn.active {
            border-bottom-color: #2563EB !important;
            color: #2563EB !important;
        }
    </style>

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

            // ✅ FILE SIZE VALIDATION CONSTANT
            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB dalam bytes

            // ===== TAB NAVIGATION LOGIC =====
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-600', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active', 'border-blue-600', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show target tab content
                    const targetContent = document.getElementById('tab-' + targetTab);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }
                });
            });
            
            // Set first tab as active by default
            if (tabButtons.length > 0) {
                tabButtons[0].click();
            }

            let optionCount = 4;
            if (optionsList) {
                optionCount = optionsList.children.length;
            }

            // ✅ IMAGE PREVIEW WITH FILE SIZE VALIDATION
            const validateAndPreviewImage = (imageInput, previewImg, imagePreview, removeBtn) => {
                if (!imageInput) return;

                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // ✅ VALIDASI UKURAN FILE
                        if (file.size > MAX_FILE_SIZE) {
                            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                            alert(
                                `❌ FILE TERLALU BESAR!\n\n` +
                                `Nama file: ${file.name}\n` +
                                `Ukuran file: ${fileSizeMB} MB\n` +
                                `Maksimal: 5 MB\n\n` +
                                `Silakan pilih file yang lebih kecil atau kompres gambar terlebih dahulu.`
                            );
                            e.target.value = ''; // Reset input
                            if (imagePreview) imagePreview.style.display = 'none';
                            if (removeBtn) removeBtn.style.display = 'none';
                            return;
                        }

                        // Preview jika valid
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

            // Apply untuk gambar pertanyaan utama
            if (questionImage && previewImg && imagePreview && removeImageBtn) {
                validateAndPreviewImage(questionImage, previewImg, imagePreview, removeImageBtn);
            }

            // Setup untuk gambar opsi
            function setupOptionImagePreview(optionItem) {
                const imageInput = optionItem.querySelector('.option-image-input');
                const previewContainer = optionItem.querySelector('.option-image-preview');
                const previewImg = optionItem.querySelector('.option-preview-img');
                const removeBtn = optionItem.querySelector('.remove-option-image-btn');

                if (imageInput && previewContainer && previewImg) {
                    validateAndPreviewImage(imageInput, previewImg, previewContainer, removeBtn);
                }
            }

            // Apply untuk semua opsi yang ada
            document.querySelectorAll('.option-item').forEach(item => {
                setupOptionImagePreview(item);
            });

            // ✅ VALIDASI SAAT SUBMIT FORM
            const questionForm = document.getElementById('questionForm');
            if (questionForm) {
                questionForm.addEventListener('submit', function(e) {
                    let oversizedFiles = [];
                    
                    // Check gambar pertanyaan utama
                    const qImg = document.getElementById('question_image');
                    if (qImg && qImg.files[0]) {
                        if (qImg.files[0].size > MAX_FILE_SIZE) {
                            const sizeMB = (qImg.files[0].size / (1024 * 1024)).toFixed(2);
                            oversizedFiles.push(`• Gambar Pertanyaan: ${sizeMB} MB`);
                        }
                    }
                    
                    // Check semua gambar opsi
                    document.querySelectorAll('.option-image-input').forEach((input, i) => {
                        if (input.files[0]) {
                            if (input.files[0].size > MAX_FILE_SIZE) {
                                const sizeMB = (input.files[0].size / (1024 * 1024)).toFixed(2);
                                const letter = String.fromCharCode(65 + i);
                                oversizedFiles.push(`• Gambar Opsi ${letter}: ${sizeMB} MB`);
                            }
                        }
                    });
                    
                    // Jika ada file yang oversized, tampilkan alert dan batalkan submit
                    if (oversizedFiles.length > 0) {
                        e.preventDefault();
                        alert(
                            `❌ TIDAK DAPAT MENYIMPAN!\n\n` +
                            `File berikut melebihi batas maksimal 5 MB:\n\n` +
                            `${oversizedFiles.join('\n')}\n\n` +
                            `Mohon ganti dengan file yang lebih kecil atau kompres gambar terlebih dahulu.`
                        );
                        
                        // Scroll ke form agar user bisa melihat
                        formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        return false;
                    }
                });
            }

            // Form Toggle
            if (toggleFormBtn && formContainer) {
                toggleFormBtn.addEventListener('click', function() {
                    const isHidden = formContainer.style.display === 'none';
                    formContainer.style.display = isHidden ? 'block' : 'none';
                    toggleFormBtn.textContent = isHidden ? 'Sembunyikan Form' : 'Tambah Soal';
                    if (isHidden) toggleContainers();
                });
            }

            if (cancelBtn && formContainer && toggleFormBtn) {
                cancelBtn.addEventListener('click', function() {
                    formContainer.style.display = 'none';
                    toggleFormBtn.textContent = 'Tambah Soal';
                });
            }

            // Update Option Labels
            function updateOptionLabels() {
                document.querySelectorAll('.option-item').forEach((item, index) => {
                    const label = item.querySelector('.option-label');
                    const input = item.querySelector('.option-input');
                    const radio = item.querySelector('.correct-radio');
                    const removeBtn = item.querySelector('.remove-option-btn');
                    const imageInput = item.querySelector('.option-image-input');
                    const hiddenIndex = item.querySelector('input[name*="[index]"]');

                    const letter = String.fromCharCode(65 + index);

                    if (label) label.textContent = `Opsi ${letter}`;
                    if (input) {
                        input.placeholder = `Masukkan teks untuk Opsi ${letter}`;
                        input.name = `options[${index}][text]`;
                    }
                    if (imageInput) imageInput.name = `options[${index}][image_file]`;
                    if (radio) radio.value = index;
                    if (hiddenIndex) hiddenIndex.value = index;

                    if (removeBtn) {
                        const isPapi = typeSelect && typeSelect.value === 'PAPIKOSTICK';
                        const totalOptions = document.querySelectorAll('.option-item').length;
                        removeBtn.style.display = totalOptions > 2 && !isPapi ? 'block' : 'none';
                    }
                });
            }

            // Toggle Containers
            function toggleContainers() {
                if (!typeSelect) return;

                const selectedType = typeSelect.value;

                // Reset/Hide All
                if (optionsSection) optionsSection.style.display = 'none';
                if (memoryContainer) memoryContainer.classList.add('hidden');
                if (papiContainer) papiContainer.classList.add('hidden');
                if (questionTextContainer) questionTextContainer.style.display = 'block';
                if (questionImageContainer) questionImageContainer.style.display = 'block';
                if (addOptionBtn) addOptionBtn.style.display = 'block';

                // Reset Labels
                const questionTextLabel = document.getElementById('question-text-label');
                const questionTextHint = document.getElementById('question-text-hint');
                const optionsHint = document.getElementById('options-hint');

                if (questionTextLabel) questionTextLabel.textContent = 'Teks Pertanyaan';
                if (questionTextHint) questionTextHint.textContent = 'Pertanyaan untuk peserta';
                if (optionsHint) optionsHint.textContent = 'Pilihan jawaban untuk pertanyaan';

                document.querySelectorAll('.option-item').forEach((item, index) => {
                    item.style.display = 'block';
                    const radioBlock = item.querySelector('.correct-radio-block');
                    const removeBtn = item.querySelector('.remove-option-btn');

                    if (radioBlock) radioBlock.style.display = 'flex';
                    if (removeBtn) removeBtn.style.display = index >= 2 ? 'block' : 'none';
                });

                // Show Containers Based on Type
                if (selectedType === 'PAPIKOSTICK') {
                    if (papiContainer) papiContainer.classList.remove('hidden');
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (questionImageContainer) questionImageContainer.style.display = 'none';

                    if (questionTextLabel) questionTextLabel.textContent = 'Nomor Soal PAPI (1-90)';
                    const questionTextInput = document.getElementById('question_text');
                    if (questionTextInput) questionTextInput.placeholder = 'Masukkan Nomor Soal (mis: 45) di sini.';
                    if (questionTextHint) questionTextHint.textContent =
                        'Kolom ini hanya untuk Nomor Soal PAPI. Teks pernyataan diisi di Opsi A dan B.';

                    if (addOptionBtn) addOptionBtn.style.display = 'none';
                    if (optionsHint) optionsHint.textContent =
                        'Hanya Opsi A dan B yang digunakan untuk Pasangan Pernyataan PAPI.';

                    document.querySelectorAll('.option-item').forEach((item, index) => {
                        const radioBlock = item.querySelector('.correct-radio-block');

                        if (index >= 2) {
                            item.style.display = 'none';
                        } else {
                            if (radioBlock) radioBlock.style.display = 'none';
                        }
                    });

                } else if (selectedType === 'ESSAY') {
                    if (optionsSection) optionsSection.style.display = 'none';
                    if (addOptionBtn) addOptionBtn.style.display = 'none';

                } else if (selectedType === 'PILIHAN_GANDA') {
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (addOptionBtn) addOptionBtn.style.display = 'block';

                } else if (selectedType === 'HAFALAN') {
                    if (memoryContainer) memoryContainer.classList.remove('hidden');
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (questionImageContainer) questionImageContainer.style.display = 'none';

                    if (questionTextLabel) questionTextLabel.textContent = '❓ Pertanyaan (setelah hafalan)';
                    if (questionTextHint) questionTextHint.textContent =
                        'Pertanyaan yang akan muncul setelah materi hafalan hilang';
                }
            }

            // Add Option
            if (addOptionBtn && optionsList) {
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
                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-1.5 mb-2 flex items-start gap-1.5">
                                        <svg class="w-3 h-3 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-xs text-yellow-800">Maks <strong class="text-red-600">5 MB</strong></p>
                                    </div>
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
                    optionCount = optionsList.children.length;
                    updateOptionLabels();
                });
            }

            // Remove Option
            if (optionsList) {
                optionsList.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-option-btn');
                    const optionItem = e.target.closest('.option-item');

                    if (removeBtn && optionItem && optionsList.children.length > 2) {
                        optionItem.remove();
                        optionCount = optionsList.children.length;
                        updateOptionLabels();
                    } else if (removeBtn) {
                        alert('Minimal harus ada 2 Opsi Jawaban.');
                    }
                });
            }

            // Initial Trigger
            if (typeSelect) {
                typeSelect.addEventListener('change', toggleContainers);
                toggleContainers();
            }

            // If validation errors, trigger again
            @if ($errors->any())
                toggleContainers();
            @endif
        });
    </script>
</x-admin-layout>