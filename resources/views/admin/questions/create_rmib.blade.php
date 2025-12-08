<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Soal RMIB untuk: ') }}{{ $AlatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('admin.questions.partials.form-header')

            {{-- Form Tambah RMIB --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">🎓 Tambah Soal RMIB</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Total soal RMIB yang ada: <strong>{{ $existingRmibCount ?? 0 }}</strong> dari 144
                        </p>
                    </div>
                    <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                        ← Kembali
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.alat-tes.questions.rmib.store', $AlatTes->id) }}"
                    enctype="multipart/form-data" id="rmibForm">
                    @csrf

                    <input type="hidden" name="type" value="RMIB">

                    {{-- ✅ CHECKBOX AUTO-GENERATE 144 SOAL RMIB --}}
                    <div
                        class="mb-6 bg-gradient-to-r from-green-100 to-teal-100 border-2 border-green-300 rounded-lg p-4">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="auto_generate_rmib" name="auto_generate_rmib" value="1"
                                {{ old('auto_generate_rmib') ? 'checked' : '' }}
                                class="mt-1 h-5 w-5 text-green-600 rounded focus:ring-green-500">
                            <div class="flex-1">
                                <span class="font-semibold text-green-900 text-lg">
                                    ✨ Generate Otomatis 144 Item RMIB Standar
                                </span>
                                <p class="text-sm text-green-700 mt-1">
                                    Centang ini untuk otomatis membuat 144 item RMIB standar.
                                </p>
                                <div class="mt-2 bg-white rounded p-2 text-xs text-gray-600">
                                    <strong>📋 Yang akan di-generate:</strong>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                                        <li>144 aktivitas/profesi standar RMIB</li>
                                        <li>12 bidang minat karir (masing-masing 12 item)</li>
                                        <li>5 skala rating (Sangat Tidak Suka - Sangat Suka)</li>
                                    </ul>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- ✅ PERINGATAN MODE MANUAL --}}
                    <div id="rmib-manual-warning"
                        class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 mb-4 {{ old('auto_generate_rmib') ? 'hidden' : '' }}">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">⚠️ Mode Input Manual</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Anda akan membuat soal RMIB satu per satu. Disarankan centang checkbox di atas.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ INPUT MANUAL --}}
                    <div id="rmib-manual-input" class="{{ old('auto_generate_rmib') ? 'hidden' : '' }}">
                        <div class="mb-4">
                            <label for="rmib_item_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Item RMIB <span class="text-red-600">*</span>
                            </label>
                            <select id="rmib_item_id" name="rmib_item_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Item RMIB --</option>
                                @if ($rmibItems->count() > 0)
                                    @foreach ($rmibItems as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('rmib_item_id') == $item->id ? 'selected' : '' }}>
                                            #{{ $item->item_number }} - {{ $item->description }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada data RMIB</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    @include('admin.questions.partials.tab-navigation')

                    {{-- TAB CONTENT --}}
                    <div class="tab-contents">
                        {{-- TAB SOAL UTAMA --}}
                        <div class="tab-content" id="tab-soal">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-sm text-green-700">
                                    {{ old('auto_generate_rmib') ? '✅ Mode Auto-Generate: 144 item akan dibuat otomatis' : 'ℹ️ Mode Manual: Pilih item RMIB dari dropdown di atas.' }}
                                </p>
                            </div>
                        </div>

                        {{-- TAB CONTOH SOAL --}}
                        <div class="tab-content hidden" id="tab-contoh">
                            <div class="mb-4">
                                <label for="example_question" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contoh Soal & Cara Menjawab (Opsional)
                                </label>
                                <textarea id="example_question" name="example_question" rows="10"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm"
                                    placeholder="Contoh: Bekerja sebagai Dokter - Pilih rating sesuai minat Anda">{{ old('example_question') }}</textarea>
                            </div>
                        </div>

                        {{-- TAB INSTRUKSI --}}
                        <div class="tab-content hidden" id="tab-instruksi">
                            <div class="mb-4">
                                <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                    Instruksi & Cara Menjawab (Opsional)
                                </label>
                                <textarea id="instructions" name="instructions" rows="10"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Jelaskan cara mengerjakan tes RMIB">{{ old('instructions') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                            <span id="submitText">Simpan Item RMIB</span>
                            <span id="submitTextAuto" class="hidden">Generate 144 Item RMIB</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const autoGenCheckbox = document.getElementById('auto_generate_rmib');
                const manualInput = document.getElementById('rmib-manual-input');
                const manualWarning = document.getElementById('rmib-manual-warning');
                const submitText = document.getElementById('submitText');
                const submitTextAuto = document.getElementById('submitTextAuto');
                const rmibItemSelect = document.getElementById('rmib_item_id');
                const rmibForm = document.getElementById('rmibForm');

                function toggleMode() {
                    if (autoGenCheckbox && autoGenCheckbox.checked) {
                        if (manualInput) manualInput.classList.add('hidden');
                        if (manualWarning) manualWarning.classList.add('hidden');
                        if (submitText) submitText.classList.add('hidden');
                        if (submitTextAuto) submitTextAuto.classList.remove('hidden');
                        if (rmibItemSelect) {
                            rmibItemSelect.required = false;
                            rmibItemSelect.removeAttribute('required'); // ✅ TAMBAHKAN INI
                        }
                    } else {
                        if (manualInput) manualInput.classList.remove('hidden');
                        if (manualWarning) manualWarning.classList.remove('hidden');
                        if (submitText) submitText.classList.remove('hidden');
                        if (submitTextAuto) submitTextAuto.classList.add('hidden');
                        if (rmibItemSelect) {
                            rmibItemSelect.required = true;
                            rmibItemSelect.setAttribute('required', 'required'); // ✅ TAMBAHKAN INI
                        }
                    }
                }

                if (autoGenCheckbox) {
                    autoGenCheckbox.addEventListener('change', toggleMode);
                    toggleMode(); // Call on page load
                }

                // ✅ TAMBAHKAN: Handle form submission
                if (rmibForm) {
                    rmibForm.addEventListener('submit', function(e) {
                        if (autoGenCheckbox && autoGenCheckbox.checked) {
                            if (rmibItemSelect) {
                                rmibItemSelect.removeAttribute('required');
                                rmibItemSelect.value = ''; // Clear value
                            }
                        }
                    });
                }

                // Tab navigation (sudah ada)
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
</x-admin-layout>
