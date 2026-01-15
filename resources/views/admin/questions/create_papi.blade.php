<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Soal PAPI Kostick: ') }}{{ $AlatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('admin.questions.partials.form-header')

            {{-- INFO BANNER --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 rounded">
                    <p class="text-green-700 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded">
                    <p class="text-red-700 font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Form Tambah PAPI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">🔷 Tambah Soal PAPI Kostick</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Total soal PAPI yang ada: <strong>{{ $existingPapiCount }}</strong> dari 90
                        </p>
                        @if ($papiItems->count() == 0)
                            <p class="text-sm text-red-600 mt-1">
                                ⚠️ Data PAPI Kostick belum ada di database. Silakan jalankan seeder terlebih dahulu.
                            </p>
                        @else
                            <p class="text-sm text-green-600 mt-1">
                                ✅ Database memiliki {{ $papiItems->count() }} item PAPI siap digunakan
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                        ← Kembali
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.alat-tes.questions.papi.store', $AlatTes->id) }}"
                    id="papiForm">
                    @csrf

                    {{-- ✅ CHECKBOX AUTO-GENERATE --}}
                    <div class="mb-6 bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-300 rounded-lg p-4">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="auto_generate_papi" name="auto_generate_papi" value="1"
                                {{ $papiItems->count() == 0 ? 'disabled' : '' }}
                                class="mt-1 h-5 w-5 text-purple-600 rounded focus:ring-purple-500">
                            <div class="flex-1">
                                <span class="font-semibold text-purple-900 text-lg">
                                    ✨ Tambahkan 90 Soal PAPI Kostick Standar Sekaligus
                                </span>
                                <p class="text-sm text-purple-700 mt-1">
                                    Centang ini untuk otomatis menggunakan 90 soal PAPI Kostick standar dari database.
                                    Anda hanya perlu mengisi <strong>Contoh Soal</strong> dan
                                    <strong>Instruksi</strong> di bawah.
                                </p>
                                <div class="mt-2 bg-white rounded p-2 text-xs text-gray-600">
                                    <strong>📋 Yang akan ditambahkan:</strong>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                                        <li>90 pasangan pernyataan dari database PAPI Kostick</li>
                                        <li>Statement A & B lengkap dengan aspect/need</li>
                                        <li>Sesuai standar PAPI Kostick internasional</li>
                                    </ul>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- ✅ PERINGATAN JIKA TIDAK CENTANG --}}
                    <div id="papi-manual-warning" class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 mb-3 hidden">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">⚠️ Mode Input Manual</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Anda akan memilih soal PAPI satu per satu dari daftar. Disarankan centang checkbox
                                    di atas untuk auto-generate.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ INPUT MANUAL (Hanya muncul jika checkbox tidak dicentang) --}}
                    <div id="papi-manual-input">
                        <div class="mb-4">
                            <label for="papi_item_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Soal PAPI <span id="required-mark" class="text-red-500">*</span>
                            </label>
                            @if ($papiItems->count() > 0)
                                <select id="papi_item_id" name="papi_item_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('papi_item_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Item PAPI --</option>
                                    @foreach ($papiItems as $item)
                                        <option value="{{ $item->id }}"
                                            data-statement-a="{{ $item->statement_a }}"
                                            data-statement-b="{{ $item->statement_b }}"
                                            data-role-a="{{ $item->role_a }}"
                                            data-role-b="{{ $item->role_b }}"
                                            {{ old('papi_item_id') == $item->id ? 'selected' : '' }}>
                                            Item {{ $item->item_number }}:
                                            {{ Str::limit($item->statement_a, 50) }} VS
                                            {{ Str::limit($item->statement_b, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('papi_item_id')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">⚠️ {{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">
                                    💡 Total {{ $papiItems->count() }} item PAPI tersedia di database
                                </p>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <p class="text-red-700 text-sm font-semibold">⚠️ Data PAPI Kostick tidak tersedia</p>
                                    <p class="text-red-600 text-xs mt-1">
                                        Silakan jalankan seeder database terlebih dahulu: <code class="bg-red-100 px-2 py-1 rounded">php artisan db:seed</code>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Preview Selected Item --}}
                        <div id="item-preview" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                            <h5 class="font-semibold text-gray-800 mb-2">Preview Soal:</h5>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-3 rounded">
                                    <p class="text-xs font-semibold text-blue-700 mb-1">Statement A:</p>
                                    <p id="preview-statement-a" class="text-sm text-gray-800"></p>
                                    <p class="text-xs text-blue-600 mt-1">Kebutuhan/Sifat: <span id="preview-aspect-a"
                                            class="font-semibold"></span></p>
                                </div>
                                <div class="bg-green-50 p-3 rounded">
                                    <p class="text-xs font-semibold text-green-700 mb-1">Statement B:</p>
                                    <p id="preview-statement-b" class="text-sm text-gray-800"></p>
                                    <p class="text-xs text-green-600 mt-1">Kebutuhan/Sifat: <span id="preview-aspect-b"
                                            class="font-semibold"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin.questions.partials.tab-navigation')

                    {{-- TAB CONTENT --}}
                    <div class="tab-contents">
                        {{-- TAB SOAL UTAMA --}}
                        <div class="tab-content" id="tab-soal">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <p id="mode-info" class="text-sm text-purple-700">
                                    ℹ️ Mode Manual: Pilih soal PAPI dari dropdown di atas.
                                </p>
                            </div>
                        </div>

                        {{-- TAB CONTOH SOAL --}}
                        <div class="tab-content hidden" id="tab-contoh">
                            <div class="mb-4">
                                <label for="example_question" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contoh Soal PAPI Kostick (Opsional)
                                </label>
                                <textarea id="example_question" name="example_question" rows="12"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm font-mono text-sm"
                                    placeholder="Contoh:&#10;&#10;Item 1:&#10;A. Saya adalah orang yang keras hati&#10;B. Saya adalah pekerja keras&#10;&#10;Pilih salah satu yang paling menggambarkan Anda.&#10;&#10;Jawaban: Pilih A atau B sesuai kepribadian Anda.">{{ old('example_question') }}</textarea>
                            </div>
                        </div>

                        {{-- TAB INSTRUKSI --}}
                        <div class="tab-content hidden" id="tab-instruksi">
                            <div class="mb-4">
                                <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                    Instruksi & Cara Menjawab (Opsional)
                                </label>
                                <textarea id="instructions" name="instructions" rows="10"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                    placeholder="Contoh:&#10;&#10;Cara mengerjakan PAPI Kostick:&#10;1. Anda akan melihat 90 pasangan pernyataan&#10;2. Setiap pasangan terdiri dari Pernyataan A dan Pernyataan B&#10;3. Pilih salah satu yang PALING menggambarkan diri Anda&#10;4. Tidak ada jawaban benar atau salah&#10;5. Jawablah dengan jujur sesuai kepribadian Anda&#10;&#10;Tips:&#10;• Jangan terlalu lama berpikir, ikuti insting pertama&#10;• Bayangkan diri Anda dalam situasi kerja&#10;• Jujurlah pada diri sendiri">{{ old('instructions') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg"
                            {{ $papiItems->count() == 0 ? 'disabled' : '' }}>
                            <span id="submitText">Simpan Soal PAPI</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const autoGenCheckbox = document.getElementById('auto_generate_papi');
                const manualInput = document.getElementById('papi-manual-input');
                const manualWarning = document.getElementById('papi-manual-warning');
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const papiItemSelect = document.getElementById('papi_item_id');
                const itemPreview = document.getElementById('item-preview');
                const papiForm = document.getElementById('papiForm');
                const modeInfo = document.getElementById('mode-info');

                // Data tidak lagi diambil melalui @json($papiItems),
                // tetapi melalui data-* attribute di <option> agar lebih efisien.
                
                function toggleMode() {
                    if (autoGenCheckbox.checked) {
                        // Mode Auto-Generate
                        manualInput.classList.add('hidden');
                        manualWarning.classList.add('hidden');
                        submitText.textContent = '✨ Generate 90 Soal PAPI';
                        
                        if (modeInfo) {
                            modeInfo.textContent = '✅ Mode Auto-Generate: 90 soal akan ditambahkan otomatis dari database PAPI Kostick.';
                        }

                        if (papiItemSelect) {
                            papiItemSelect.removeAttribute('required');
                            // Reset nilai select jika beralih ke auto-generate
                            papiItemSelect.value = ''; 
                            itemPreview.classList.add('hidden');
                        }
                    } else {
                        // Mode Manual
                        manualInput.classList.remove('hidden');
                        manualWarning.classList.remove('hidden');
                        submitText.textContent = 'Simpan Soal PAPI';
                        
                        if (modeInfo) {
                            modeInfo.textContent = 'ℹ️ Mode Manual: Pilih soal PAPI dari dropdown di atas.';
                        }

                        if (papiItemSelect) {
                            papiItemSelect.setAttribute('required', 'required');
                            // Panggil fungsi preview jika ada nilai lama yang dipilih
                            updatePreview(); 
                        }
                    }
                }

                function updatePreview() {
                    const selectedOption = papiItemSelect.options[papiItemSelect.selectedIndex];
                    const selectedId = selectedOption ? selectedOption.value : null;

                    if (selectedId) {
                        // Ambil data dari data-* attributes
                        const statementA = selectedOption.getAttribute('data-statement-a');
                        const statementB = selectedOption.getAttribute('data-statement-b');
                        const roleA = selectedOption.getAttribute('data-role-a');
                        const roleB = selectedOption.getAttribute('data-role-b');

                        document.getElementById('preview-statement-a').textContent = statementA;
                        document.getElementById('preview-statement-b').textContent = statementB;
                        document.getElementById('preview-aspect-a').textContent = roleA || 'N/A';
                        document.getElementById('preview-aspect-b').textContent = roleB || 'N/A';
                        itemPreview.classList.remove('hidden');
                    } else {
                        itemPreview.classList.add('hidden');
                    }
                }

                // Validasi form sebelum submit
                if (papiForm) {
                    papiForm.addEventListener('submit', function(e) {
                        // Hanya validasi jika TIDAK auto-generate
                        if (!autoGenCheckbox.checked) {
                            if (!papiItemSelect || !papiItemSelect.value || papiItemSelect.value === '') {
                                e.preventDefault();

                                // Tampilkan alert
                                alert('⚠️ WAJIB: Silakan pilih soal PAPI dari dropdown terlebih dahulu!\n\nAnda sedang dalam mode input manual, sehingga harus memilih satu item PAPI dari daftar yang tersedia.');

                                // Highlight dropdown dengan border merah
                                if (papiItemSelect) {
                                    papiItemSelect.focus();
                                    papiItemSelect.classList.add('border-red-500', 'border-2');
                                    papiItemSelect.classList.remove('border-gray-300');

                                    // Hilangkan highlight setelah 3 detik
                                    setTimeout(() => {
                                        papiItemSelect.classList.remove('border-red-500', 'border-2');
                                        papiItemSelect.classList.add('border-gray-300');
                                    }, 3000);
                                }

                                // Scroll ke dropdown
                                if (manualInput) {
                                    manualInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }

                                return false;
                            }
                        }

                        // Jika auto-generate, pastikan checkbox value terkirim
                        return true;
                    });
                }

                if (autoGenCheckbox) {
                    autoGenCheckbox.addEventListener('change', toggleMode);
                    toggleMode(); // Initial state
                }

                // Preview selected PAPI item
                if (papiItemSelect) {
                    papiItemSelect.addEventListener('change', updatePreview);
                    
                    // Panggil updatePreview untuk menampilkan preview jika ada nilai 'old'
                    if (papiItemSelect.value) {
                         updatePreview(); 
                    }
                }

                // Tab navigation
                const tabButtons = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');

                        tabButtons.forEach(btn => {
                            btn.classList.remove('active', 'border-blue-600', 'text-blue-600');
                            btn.classList.add('border-transparent', 'text-gray-500');
                        });

                        this.classList.add('active', 'border-blue-600', 'text-blue-600');
                        this.classList.remove('border-transparent', 'text-gray-500');

                        tabContents.forEach(content => content.classList.add('hidden'));

                        const targetContent = document.getElementById('tab-' + targetTab);
                        if (targetContent) targetContent.classList.remove('hidden');
                    });
                });

                if (tabButtons.length > 0) {
                    tabButtons[0].click();
                }
            });
        </script>
    @endpush

    <style>
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
</x-admin-layout>