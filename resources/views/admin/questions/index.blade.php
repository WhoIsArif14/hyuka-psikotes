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
                                    Ganda (Satu Jawaban)</option>
                                <option value="PILIHAN_GANDA_KOMPLEKS"
                                    {{ old('type') == 'PILIHAN_GANDA_KOMPLEKS' ? 'selected' : '' }}>Pilihan
                                    Ganda Kompleks (Banyak Jawaban)</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai</option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan
                                </option>
                                <option value="PAPIKOSTICK" {{ old('type') == 'PAPIKOSTICK' ? 'selected' : '' }}>PAPI
                                    KOSTICK (Pasangan Pernyataan)</option>
                                <option value="RMIB" {{ old('type') == 'RMIB' ? 'selected' : '' }}>RMIB (Tes Minat
                                    Karir)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1" id="type-hint">Pilih jenis soal yang akan dibuat</p>
                        </div>

                        {{-- ✅ KATEGORI PERANGKINGAN --}}
                        <div id="ranking-category-container"
                            class="mb-4 border border-indigo-200 bg-indigo-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold text-indigo-700 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                📊 Kategori Perangkingan
                            </h4>
                            <p class="text-xs text-gray-600 mb-3">Kelompokkan soal berdasarkan aspek (Logika, Verbal,
                                Numerik, dll) untuk analisis skor per kategori</p>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="ranking_category" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kategori <span class="text-xs text-gray-500">(Opsional)</span>
                                    </label>
                                    <select id="ranking_category" name="ranking_category"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Tanpa Kategori --</option>
                                        <option value="LOGIKA"
                                            {{ old('ranking_category') == 'LOGIKA' ? 'selected' : '' }}>Logika</option>
                                        <option value="VERBAL"
                                            {{ old('ranking_category') == 'VERBAL' ? 'selected' : '' }}>Verbal</option>
                                        <option value="NUMERIK"
                                            {{ old('ranking_category') == 'NUMERIK' ? 'selected' : '' }}>Numerik
                                        </option>
                                        <option value="SPASIAL"
                                            {{ old('ranking_category') == 'SPASIAL' ? 'selected' : '' }}>Spasial
                                        </option>
                                        <option value="MEMORI"
                                            {{ old('ranking_category') == 'MEMORI' ? 'selected' : '' }}>Memori</option>
                                        <option value="PERHATIAN"
                                            {{ old('ranking_category') == 'PERHATIAN' ? 'selected' : '' }}>Perhatian
                                        </option>
                                        <option value="KECEPATAN"
                                            {{ old('ranking_category') == 'KECEPATAN' ? 'selected' : '' }}>Kecepatan
                                        </option>
                                        <option value="CUSTOM"
                                            {{ old('ranking_category') == 'CUSTOM' ? 'selected' : '' }}>⚙️ Custom
                                            (Ketik Manual)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="ranking_weight" class="block text-sm font-medium text-gray-700 mb-1">
                                        Bobot Soal <span class="text-xs text-gray-500">(1-100)</span>
                                    </label>
                                    <input type="number" id="ranking_weight" name="ranking_weight" min="1"
                                        max="100" value="{{ old('ranking_weight', 1) }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Default: 1 (Semakin tinggi, semakin penting)
                                    </p>
                                </div>
                            </div>

                            {{-- Custom Category Input --}}
                            <div id="custom-category-input" class="mt-3 hidden">
                                <label for="custom_ranking_category"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Kategori Custom
                                </label>
                                <input type="text" id="custom_ranking_category"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Contoh: Analisis Verbal, Reasoning, dll" maxlength="100">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 100 karakter</p>
                            </div>

                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-800">
                                    <strong>💡 Cara Kerja:</strong> Kategori ini digunakan untuk menghitung skor per
                                    kategori dan membuat ranking berdasarkan aspek tertentu. Bobot mempengaruhi
                                    kontribusi soal terhadap skor total.
                                </p>
                            </div>
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

                        {{-- ✅ Input Khusus RMIB --}}
                        <div id="rmib-scoring-container"
                            class="border border-green-200 bg-green-50 p-4 rounded-lg mb-4 hidden">
                            <h4 class="text-md font-semibold text-green-700 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                </svg>
                                ⚙️ Pengaturan RMIB (Rothwell Miller Interest Blank)
                            </h4>
                            <p class="text-xs text-gray-600 mb-3">Tes minat karir yang mengukur 12 bidang minat
                                vocational</p>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="rmib_item_number" class="block text-sm font-medium text-gray-700">
                                        Nomor Soal RMIB (1-144)
                                    </label>
                                    <input id="rmib_item_number" name="rmib_item_number" type="number"
                                        min="1" max="144" value="{{ old('rmib_item_number') }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                                    <p class="text-xs text-gray-500 mt-1">RMIB memiliki 144 item soal</p>
                                </div>

                                <div>
                                    <label for="rmib_interest_area" class="block text-sm font-medium text-gray-700">
                                        Bidang Minat (Interest Area)
                                    </label>
                                    <select id="rmib_interest_area" name="rmib_interest_area"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                                        <option value="">-- Pilih Bidang Minat --</option>
                                        <option value="OUTDOOR"
                                            {{ old('rmib_interest_area') == 'OUTDOOR' ? 'selected' : '' }}>
                                            1. Outdoor (Alam Terbuka)
                                        </option>
                                        <option value="MECHANICAL"
                                            {{ old('rmib_interest_area') == 'MECHANICAL' ? 'selected' : '' }}>
                                            2. Mechanical (Mekanik)
                                        </option>
                                        <option value="COMPUTATIONAL"
                                            {{ old('rmib_interest_area') == 'COMPUTATIONAL' ? 'selected' : '' }}>
                                            3. Computational (Komputasi)
                                        </option>
                                        <option value="SCIENTIFIC"
                                            {{ old('rmib_interest_area') == 'SCIENTIFIC' ? 'selected' : '' }}>
                                            4. Scientific (Ilmiah)
                                        </option>
                                        <option value="PERSONAL_CONTACT"
                                            {{ old('rmib_interest_area') == 'PERSONAL_CONTACT' ? 'selected' : '' }}>
                                            5. Personal Contact (Kontak Personal)
                                        </option>
                                        <option value="AESTHETIC"
                                            {{ old('rmib_interest_area') == 'AESTHETIC' ? 'selected' : '' }}>
                                            6. Aesthetic (Estetika)
                                        </option>
                                        <option value="LITERARY"
                                            {{ old('rmib_interest_area') == 'LITERARY' ? 'selected' : '' }}>
                                            7. Literary (Sastra)
                                        </option>
                                        <option value="MUSICAL"
                                            {{ old('rmib_interest_area') == 'MUSICAL' ? 'selected' : '' }}>
                                            8. Musical (Musik)
                                        </option>
                                        <option value="SOCIAL_SERVICE"
                                            {{ old('rmib_interest_area') == 'SOCIAL_SERVICE' ? 'selected' : '' }}>
                                            9. Social Service (Layanan Sosial)
                                        </option>
                                        <option value="CLERICAL"
                                            {{ old('rmib_interest_area') == 'CLERICAL' ? 'selected' : '' }}>
                                            10. Clerical (Administrasi)
                                        </option>
                                        <option value="PRACTICAL"
                                            {{ old('rmib_interest_area') == 'PRACTICAL' ? 'selected' : '' }}>
                                            11. Practical (Praktis)
                                        </option>
                                        <option value="MEDICAL"
                                            {{ old('rmib_interest_area') == 'MEDICAL' ? 'selected' : '' }}>
                                            12. Medical (Medis)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 bg-green-100 border border-green-300 rounded-lg p-3">
                                <p class="text-xs text-green-900 font-semibold mb-2">📋 Tentang RMIB:</p>
                                <ul class="text-xs text-green-800 space-y-1 list-disc list-inside">
                                    <li>Total 144 item yang mengukur 12 bidang minat karir</li>
                                    <li>Setiap bidang minat diukur oleh 12 item soal</li>
                                    <li>Format: Peserta memilih aktivitas yang paling mereka sukai</li>
                                    <li>Tidak ada jawaban benar/salah, hanya preferensi minat</li>
                                    <li>Hasil: Profil minat karir untuk pengarahan vocational</li>
                                </ul>
                            </div>

                            <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-xs text-yellow-800">
                                    <strong>⚠️ Penting:</strong> Pastikan nomor item dan bidang minat sesuai dengan
                                    standar RMIB.
                                    Untuk pengisian massal, gunakan fitur import Excel.
                                </p>
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
                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-blue-900 mb-1">Soal yang akan dijawab peserta
                                            </h3>
                                            <p class="text-sm text-blue-700">Ini adalah soal aktual yang akan diberikan
                                                kepada peserta dalam tes.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- MATERI HAFALAN CONTAINER --}}
                                <div id="memory-container"
                                    class="border border-indigo-200 bg-indigo-50 p-4 rounded-lg mb-4 hidden">
                                    <h4 class="text-md font-semibold text-indigo-700 mb-3">📚 Materi Hafalan</h4>
                                    <p class="text-sm text-gray-600 mb-3">Materi ini akan ditampilkan selama beberapa
                                        detik,
                                        kemudian peserta akan menjawab pertanyaan di bawah.</p>

                                    <div class="mb-3">
                                        <label for="memory_content"
                                            class="block text-sm font-medium text-gray-700">Konten
                                            Memori</label>
                                        <textarea id="memory_content" name="memory_content" rows="4"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                            placeholder="Contoh: BUNGA - Dahlia, Flamboyan, Laret, Soka, Yasmin&#10;PERKAKAS - Cangkul, Jarum, Kikir, Palu, Wajan">{{ old('memory_content') }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">Masukkan materi yang harus dihafal
                                            peserta</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="memory_type"
                                                class="block text-sm font-medium text-gray-700">Tipe
                                                Konten</label>
                                            <select id="memory_type" name="memory_type"
                                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                                <option value="TEXT"
                                                    {{ old('memory_type', 'TEXT') == 'TEXT' ? 'selected' : '' }}>Teks
                                                </option>
                                                <option value="IMAGE"
                                                    {{ old('memory_type') == 'IMAGE' ? 'selected' : '' }}>
                                                    Gambar</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="duration_seconds"
                                                class="block text-sm font-medium text-gray-700">Durasi
                                                Tampil (Detik)</label>
                                            <input id="duration_seconds" name="duration_seconds" type="number"
                                                min="1" value="{{ old('duration_seconds', 10) }}"
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

                                    <div
                                        class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 mb-2 flex items-start gap-2">
                                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-xs text-yellow-800">
                                            <strong>Perhatian:</strong> Ukuran file maksimal <strong
                                                class="text-red-600">5 MB</strong>. File yang lebih besar akan ditolak.
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <input type="file" id="question_image" name="question_image"
                                            accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <button type="button" id="removeImageBtn" style="display: none;"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Hapus
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. <span
                                            class="text-red-600 font-semibold">Maksimal 5MB</span></p>

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
                                    <p class="text-xs text-gray-500 mt-1" id="question-text-hint">Pertanyaan untuk
                                        peserta</p>
                                </div>
                            </div>

                            {{-- TAB CONTOH SOAL --}}
                            <div class="tab-content hidden" id="tab-contoh">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-green-900 mb-1">Contoh untuk pemahaman
                                                peserta</h3>
                                            <p class="text-sm text-green-700">Berikan contoh soal serupa dengan
                                                jawabannya untuk membantu peserta memahami format tes.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="example_question"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Contoh Soal & Pembahasan (Opsional)
                                    </label>
                                    <textarea id="example_question" name="example_question" rows="12"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                        placeholder="Contoh:&#10;&#10;Soal: Jika 2 + 3 = 5, maka 4 + 5 = ?&#10;&#10;Pilihan:&#10;A. 7&#10;B. 8&#10;C. 9&#10;D. 10&#10;&#10;Jawaban: C (9)&#10;&#10;Pembahasan: &#10;Kita tinggal menjumlahkan kedua angka, 4 + 5 = 9. Pola yang sama dengan contoh di soal awal.">{{ old('example_question') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-2">💡 Berikan contoh lengkap dengan jawaban dan
                                        pembahasannya agar peserta paham format soal</p>
                                </div>
                            </div>

                            {{-- TAB INSTRUKSI --}}
                            <div class="tab-content hidden" id="tab-instruksi">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-purple-900 mb-1">Panduan cara mengerjakan
                                            </h3>
                                            <p class="text-sm text-purple-700">Jelaskan cara mengerjakan soal, tips,
                                                dan hal-hal yang perlu diperhatikan peserta.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Instruksi & Cara Menjawab (Opsional)
                                    </label>
                                    <textarea id="instructions" name="instructions" rows="10"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Contoh:&#10;&#10;Cara mengerjakan:&#10;1. Baca soal dengan teliti dan pahami apa yang ditanyakan&#10;2. Perhatikan kata kunci dalam soal&#10;3. Analisis setiap pilihan jawaban dengan hati-hati&#10;4. Pilih jawaban yang paling tepat&#10;5. Periksa kembali jawaban Anda sebelum melanjutkan&#10;&#10;Tips penting:&#10;• Kerjakan soal yang mudah terlebih dahulu&#10;• Jangan terlalu lama di satu soal (maks 2 menit)&#10;• Eliminasi jawaban yang jelas salah&#10;• Jika ragu, gunakan logika dan intuisi Anda&#10;&#10;Perhatian:&#10;⚠️ Jawaban tidak bisa diubah setelah diklik&#10;⚠️ Pastikan pilihan Anda sudah benar">{{ old('instructions') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-2">💡 Berikan instruksi yang jelas dan mudah
                                        dipahami untuk membantu peserta</p>
                                </div>
                            </div>
                        </div>

                        {{-- PREVIEW INFO --}}
                        <div
                            class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                    </path>
                                </svg>
                                Urutan Tampilan untuk peserta
                            </h4>
                            <div class="space-y-2 text-sm text-gray-700 ml-7">
                                <p><span class="font-semibold">1. Instruksi</span> → Ditampilkan di awal sebelum
                                    peserta mulai tes</p>
                                <p><span class="font-semibold">2. Contoh Soal</span> → Ditampilkan untuk pemahaman
                                    format soal</p>
                                <p><span class="font-semibold">3. Soal Utama</span> → Soal yang akan dijawab oleh
                                    peserta</p>
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

                            {{-- INFO MULTIPLE ANSWERS --}}
                            <div id="multiple-answers-info"
                                class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3 hidden">
                                <div class="flex gap-2">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800">Mode: Pilihan Ganda Kompleks
                                            (Banyak Jawaban Benar)</p>
                                        <p class="text-xs text-amber-700 mt-1">✅ Centang semua opsi yang merupakan
                                            jawaban benar. Peserta harus memilih SEMUA jawaban benar untuk mendapat poin
                                            penuh.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- INFO RMIB RATING --}}
                            <div id="rmib-rating-info"
                                class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3 hidden">
                                <div class="flex gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-800">Mode: RMIB Interest Rating</p>
                                        <p class="text-xs text-green-700 mt-1">⭐ Peserta akan memberikan rating untuk
                                            setiap aktivitas berdasarkan tingkat ketertarikan mereka.</p>
                                    </div>
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
                                                        <label
                                                            class="ml-2 text-sm text-gray-600 correct-label">Benar</label>
                                                    </div>
                                                    <div class="flex items-center pt-2 correct-checkbox-block hidden">
                                                        <input type="checkbox" name="correct_answers[]"
                                                            value="{{ $i }}"
                                                            class="h-4 w-4 text-green-600 rounded correct-checkbox"
                                                            {{ is_array(old('correct_answers')) && in_array($i, old('correct_answers')) ? 'checked' : '' }}>
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

                                                            <div
                                                                class="bg-yellow-50 border border-yellow-200 rounded p-1.5 mb-2 flex items-start gap-1.5">
                                                                <svg class="w-3 h-3 text-yellow-600 flex-shrink-0 mt-0.5"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                <p class="text-xs text-yellow-800">Maks <strong
                                                                        class="text-red-600">5 MB</strong></p>
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
            const rmibContainer = document.getElementById('rmib-scoring-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionImageContainer = document.getElementById('question-image-container');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');

            const questionImage = document.getElementById('question_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');

            // ✅ RANKING CATEGORY ELEMENTS
            const rankingCategorySelect = document.getElementById('ranking_category');
            const customCategoryInput = document.getElementById('custom-category-input');
            const customCategoryField = document.getElementById('custom_ranking_category');
            const rankingContainer = document.getElementById('ranking-category-container');

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
                        formContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        return false;
                    }

                    // ✅ HANDLE CUSTOM CATEGORY SUBMISSION
                    if (rankingCategorySelect && rankingCategorySelect.value === 'CUSTOM' &&
                        customCategoryField && customCategoryField.value.trim()) {
                        const customValue = customCategoryField.value.trim().toUpperCase();

                        // Create hidden input with custom value
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'ranking_category';
                        hiddenInput.value = customValue;
                        questionForm.appendChild(hiddenInput);

                        // Disable the select to avoid double submission
                        rankingCategorySelect.disabled = true;
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
                    const checkbox = item.querySelector('.correct-checkbox');
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
                    if (checkbox) checkbox.value = index;
                    if (hiddenIndex) hiddenIndex.value = index;

                    if (removeBtn) {
                        const isPapi = typeSelect && typeSelect.value === 'PAPIKOSTICK';
                        const isRMIB = typeSelect && typeSelect.value === 'RMIB';
                        const totalOptions = document.querySelectorAll('.option-item').length;
                        // PAPI harus 2 opsi, RMIB biasanya 5 opsi (rating scale)
                        removeBtn.style.display = totalOptions > 2 && !isPapi && !isRMIB ? 'block' : 'none';
                    }
                });
            }

            // ✅ HANDLE CUSTOM CATEGORY INPUT VISIBILITY
            if (rankingCategorySelect && customCategoryInput) {
                rankingCategorySelect.addEventListener('change', function() {
                    if (this.value === 'CUSTOM') {
                        customCategoryInput.classList.remove('hidden');
                        if (customCategoryField) customCategoryField.required = false;
                    } else {
                        customCategoryInput.classList.add('hidden');
                        if (customCategoryField) {
                            customCategoryField.required = false;
                            customCategoryField.value = '';
                        }
                    }
                });
            }

            // ✅ ENHANCED Toggle Containers WITH RMIB SUPPORT
            function toggleContainers() {
                if (!typeSelect) return;

                const selectedType = typeSelect.value;
                const multipleAnswersInfo = document.getElementById('multiple-answers-info');
                const rmibRatingInfo = document.getElementById('rmib-rating-info');
                const typeHint = document.getElementById('type-hint');

                // Reset/Hide All
                if (optionsSection) optionsSection.style.display = 'none';
                if (memoryContainer) memoryContainer.classList.add('hidden');
                if (papiContainer) papiContainer.classList.add('hidden');
                if (rmibContainer) rmibContainer.classList.add('hidden');
                if (questionTextContainer) questionTextContainer.style.display = 'block';
                if (questionImageContainer) questionImageContainer.style.display = 'block';
                if (addOptionBtn) addOptionBtn.style.display = 'block';
                if (multipleAnswersInfo) multipleAnswersInfo.classList.add('hidden');
                if (rmibRatingInfo) rmibRatingInfo.classList.add('hidden');

                // ✅ SHOW/HIDE RANKING CATEGORY BASED ON TYPE
                if (rankingContainer) {
                    if (selectedType === 'PAPIKOSTICK') {
                        rankingContainer.classList.add('hidden');
                    } else {
                        rankingContainer.classList.remove('hidden');
                    }
                }

                // Reset Labels
                const questionTextLabel = document.getElementById('question-text-label');
                const questionTextHint = document.getElementById('question-text-hint');
                const optionsHint = document.getElementById('options-hint');

                if (questionTextLabel) questionTextLabel.textContent = 'Teks Pertanyaan';
                if (questionTextHint) questionTextHint.textContent = 'Pertanyaan untuk peserta';
                if (optionsHint) optionsHint.textContent = 'Pilihan jawaban untuk pertanyaan';

                // ✅ TOGGLE BETWEEN RADIO, CHECKBOX, AND NONE
                const isMultipleChoice = selectedType === 'PILIHAN_GANDA_KOMPLEKS';
                const isRMIB = selectedType === 'RMIB';

                document.querySelectorAll('.option-item').forEach((item, index) => {
                    item.style.display = 'block';
                    const radioBlock = item.querySelector('.correct-radio-block');
                    const checkboxBlock = item.querySelector('.correct-checkbox-block');
                    const removeBtn = item.querySelector('.remove-option-btn');

                    if (isMultipleChoice) {
                        if (radioBlock) radioBlock.style.display = 'none';
                        if (checkboxBlock) checkboxBlock.style.display = 'flex';
                        if (multipleAnswersInfo) multipleAnswersInfo.classList.remove('hidden');
                        if (typeHint) typeHint.textContent =
                            'Peserta harus memilih SEMUA jawaban yang benar';
                    } else if (isRMIB) {
                        // ✅ RMIB: Hide correct answer controls (no right/wrong answer)
                        if (radioBlock) radioBlock.style.display = 'none';
                        if (checkboxBlock) checkboxBlock.style.display = 'none';
                        if (rmibRatingInfo) rmibRatingInfo.classList.remove('hidden');
                        if (typeHint) typeHint.textContent = 'Tes minat karir tanpa jawaban benar/salah';
                    } else {
                        if (radioBlock) radioBlock.style.display = 'flex';
                        if (checkboxBlock) checkboxBlock.style.display = 'none';
                        if (typeHint) typeHint.textContent = 'Pilih jenis soal yang akan dibuat';
                    }

                    if (removeBtn) removeBtn.style.display = index >= 2 ? 'block' : 'none';
                });

                // ✅ RMIB SPECIFIC HANDLING
                if (selectedType === 'RMIB') {
                    if (rmibContainer) rmibContainer.classList.remove('hidden');
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (addOptionBtn) addOptionBtn.style.display = 'none'; // Fixed 5 options for rating

                    if (questionTextLabel) questionTextLabel.textContent = 'Deskripsi Aktivitas/Item';
                    if (questionTextHint) questionTextHint.textContent =
                        'Jelaskan aktivitas yang akan dinilai peserta';
                    if (optionsHint) optionsHint.textContent = 'Rating scale untuk mengukur tingkat ketertarikan';

                    // Ensure 5 options for RMIB (rating scale)
                    ensureOptionCount(5);

                    // Set rating labels
                    const ratingLabels = [
                        'Sangat Tidak Suka',
                        'Tidak Suka',
                        'Netral',
                        'Suka',
                        'Sangat Suka'
                    ];

                    document.querySelectorAll('.option-item').forEach((item, index) => {
                        if (index < 5) {
                            const label = item.querySelector('.option-label');
                            const input = item.querySelector('.option-input');
                            const radioBlock = item.querySelector('.correct-radio-block');
                            const checkboxBlock = item.querySelector('.correct-checkbox-block');
                            const removeBtn = item.querySelector('.remove-option-btn');

                            if (label) label.textContent = ratingLabels[index];
                            if (input) {
                                input.value = ratingLabels[index];
                                input.placeholder = ratingLabels[index];
                                input.readOnly = true; // Lock the rating text
                            }
                            if (radioBlock) radioBlock.style.display = 'none';
                            if (checkboxBlock) checkboxBlock.style.display = 'none';
                            if (removeBtn) removeBtn.style.display = 'none';
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }

                // PAPIKOSTICK SPECIFIC HANDLING
                else if (selectedType === 'PAPIKOSTICK') {
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

                    ensureOptionCount(2);

                    document.querySelectorAll('.option-item').forEach((item, index) => {
                        const radioBlock = item.querySelector('.correct-radio-block');
                        const checkboxBlock = item.querySelector('.correct-checkbox-block');
                        const removeBtn = item.querySelector('.remove-option-btn');

                        if (index >= 2) {
                            item.style.display = 'none';
                        } else {
                            if (radioBlock) radioBlock.style.display = 'none';
                            if (checkboxBlock) checkboxBlock.style.display = 'none';
                            if (removeBtn) removeBtn.style.display = 'none';
                            item.style.display = 'block';
                        }
                    });
                }

                // ESSAY HANDLING
                else if (selectedType === 'ESSAY') {
                    if (optionsSection) optionsSection.style.display = 'none';
                    if (addOptionBtn) addOptionBtn.style.display = 'none';
                }

                // PILIHAN_GANDA / PILIHAN_GANDA_KOMPLEKS
                else if (selectedType === 'PILIHAN_GANDA' || selectedType === 'PILIHAN_GANDA_KOMPLEKS') {
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (addOptionBtn) addOptionBtn.style.display = 'block';
                    ensureOptionCount(4);
                }

                // HAFALAN HANDLING
                else if (selectedType === 'HAFALAN') {
                    if (memoryContainer) memoryContainer.classList.remove('hidden');
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (questionImageContainer) questionImageContainer.style.display = 'none';

                    if (questionTextLabel) questionTextLabel.textContent = '❓ Pertanyaan (setelah hafalan)';
                    if (questionTextHint) questionTextHint.textContent =
                        'Pertanyaan yang akan muncul setelah materi hafalan hilang';

                    ensureOptionCount(4);
                }
            }

            // ✅ FUNCTION TO ENSURE SPECIFIC OPTION COUNT
            function ensureOptionCount(count) {
                const currentOptions = document.querySelectorAll('.option-item');
                const currentCount = currentOptions.length;

                if (currentCount < count) {
                    // Add more options
                    for (let i = currentCount; i < count; i++) {
                        addNewOption();
                    }
                } else if (currentCount > count) {
                    // Remove extra options
                    currentOptions.forEach((item, index) => {
                        if (index >= count) {
                            item.style.display = 'none';
                        } else {
                            item.style.display = 'block';
                        }
                    });
                }

                updateOptionLabels();
            }

            // ✅ ADD NEW OPTION FUNCTION
            function addNewOption() {
                if (!optionsList) return;

                const newIndex = optionsList.children.length;
                const letter = String.fromCharCode(65 + newIndex);
                const isMultipleChoice = typeSelect && typeSelect.value === 'PILIHAN_GANDA_KOMPLEKS';

                const newOption = document.createElement('div');
                newOption.className = 'option-item bg-gray-50 p-3 rounded-lg';
                newOption.innerHTML = `
            <div class="flex items-start space-x-3 w-full">
                <div class="flex items-start space-x-3 w-full">
                    <div class="flex items-center pt-2 correct-radio-block" style="${isMultipleChoice ? 'display: none;' : ''}">
                        <input type="radio" name="is_correct" value="${newIndex}" class="h-4 w-4 text-green-600 correct-radio">
                        <label class="ml-2 text-sm text-gray-600">Benar</label>
                    </div>
                    <div class="flex items-center pt-2 correct-checkbox-block" style="${isMultipleChoice ? '' : 'display: none;'}">
                        <input type="checkbox" name="correct_answers[]" value="${newIndex}" class="h-4 w-4 text-green-600 rounded correct-checkbox">
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
            }

            // Add Option Button Handler
            if (addOptionBtn && optionsList) {
                addOptionBtn.addEventListener('click', addNewOption);
            }

            // Remove Option Handler
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

            if (typeSelect) {
                typeSelect.addEventListener('change', toggleContainers);
                toggleContainers();
            }

            // If validation errors, trigger again
            @if($errors->any())
                toggleContainers();
            @endif
        });
    </script>
</x-admin-layout>
