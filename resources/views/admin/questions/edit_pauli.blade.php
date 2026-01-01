<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pauli Test') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.pauli.index', $alatTes->id) }}"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Test
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.pauli.update', $pauliTest->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Alat Tes (readonly) -->
                        <div>
                            <label for="alat_tes_id" class="block text-sm font-medium text-gray-700">
                                Alat Tes
                            </label>
                            <div class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 p-3 text-sm">
                                {{ $alatTes->nama }}
                            </div>
                            <input type="hidden" name="alat_tes_id" value="{{ $alatTes->id }}">
                        </div>

                        {{-- ✅ CHECKBOX AUTO-GENERATE PAULI (visible but disabled for edit) --}}
                        <div
                            class="mb-6 bg-gradient-to-r from-orange-100 to-yellow-50 border-2 border-orange-300 rounded-lg p-4">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" id="auto_generate_pauli" name="auto_generate_pauli"
                                    value="1" class="mt-1 h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                                <div class="flex-1">
                                    <span class="font-semibold text-orange-900 text-lg">
                                        ✨ Generate Ulang Pauli Default
                                    </span>
                                    <p class="text-sm text-orange-700 mt-1">
                                        Centang ini lalu tekan <strong>Update Test</strong> untuk mengganti konfigurasi
                                        menjadi default (45 kolom, 45 pasangan/kolom, 60 detik per kolom).
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Total Columns -->
                        <div>
                            <label for="total_columns" class="block text-sm font-medium text-gray-700">
                                Total Kolom <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="total_columns" name="total_columns"
                                value="{{ old('total_columns', $pauliTest->total_columns ?? 45) }}" min="1"
                                max="60"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('total_columns') border-red-500 @enderror"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Jumlah kolom soal (1-60). Default: 45</p>
                            @error('total_columns')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pairs Per Column -->
                        <div>
                            <label for="pairs_per_column" class="block text-sm font-medium text-gray-700">
                                Pasangan Angka Per Kolom <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="pairs_per_column" name="pairs_per_column"
                                value="{{ old('pairs_per_column', $pauliTest->pairs_per_column ?? 45) }}" min="1"
                                max="60"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('pairs_per_column') border-red-500 @enderror"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Jumlah pasangan angka per kolom (1-60). Default: 45
                            </p>
                            @error('pairs_per_column')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Per Column -->
                        <div>
                            <label for="time_per_column" class="block text-sm font-medium text-gray-700">
                                Waktu Per Kolom (detik) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="time_per_column" name="time_per_column"
                                value="{{ old('time_per_column', $pauliTest->time_per_column ?? 60) }}" min="10"
                                max="300"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('time_per_column') border-red-500 @enderror"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Waktu yang diberikan per kolom dalam detik (10-300).
                                Default: 60</p>
                            @error('time_per_column')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.pauli.index', $alatTes->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                            <button type="submit" id="submitBtn"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span id="submitText">Update Test</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const totalColumnsInput = document.getElementById('total_columns');
                const pairsPerColumnInput = document.getElementById('pairs_per_column');
                const timePerColumnInput = document.getElementById('time_per_column');

                function updatePreview() {
                    const totalColumns = parseInt(totalColumnsInput.value) || 0;
                    const pairsPerColumn = parseInt(pairsPerColumnInput.value) || 0;
                    const timePerColumn = parseInt(timePerColumnInput.value) || 0;

                    const totalQuestions = totalColumns * pairsPerColumn;
                    const totalTimeMinutes = Math.round((totalColumns * timePerColumn) / 60);
                    const timePerQuestion = totalQuestions > 0 ? ((totalColumns * timePerColumn) / totalQuestions)
                        .toFixed(1) : 0;

                    const totalQuestionsEl = document.getElementById('totalQuestions');
                    const totalTimeEl = document.getElementById('totalTime');
                    const timePerQuestionEl = document.getElementById('timePerQuestion');

                    if (totalQuestionsEl) totalQuestionsEl.textContent = totalQuestions.toLocaleString();
                    if (totalTimeEl) totalTimeEl.textContent = totalTimeMinutes;
                    if (timePerQuestionEl) timePerQuestionEl.textContent = timePerQuestion;
                }

                const autoGenCheckbox = document.getElementById('auto_generate_pauli');
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');

                function toggleAutoGen() {
                    if (!autoGenCheckbox) return;
                    if (autoGenCheckbox.checked) {
                        totalColumnsInput.setAttribute('disabled', 'disabled');
                        pairsPerColumnInput.setAttribute('disabled', 'disabled');
                        timePerColumnInput.setAttribute('disabled', 'disabled');
                        submitText.textContent = '✨ Generate Pauli Default';
                    } else {
                        totalColumnsInput.removeAttribute('disabled');
                        pairsPerColumnInput.removeAttribute('disabled');
                        timePerColumnInput.removeAttribute('disabled');
                        submitText.textContent = 'Update Test';
                    }
                }

                if (autoGenCheckbox) {
                    autoGenCheckbox.addEventListener('change', toggleAutoGen);
                    toggleAutoGen();
                }

                totalColumnsInput.addEventListener('input', updatePreview);
                pairsPerColumnInput.addEventListener('input', updatePreview);
                timePerColumnInput.addEventListener('input', updatePreview);

                updatePreview();
            });
        </script>
    @endpush
</x-admin-layout>
