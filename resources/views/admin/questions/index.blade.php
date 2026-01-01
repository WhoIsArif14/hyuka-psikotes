<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Soal untuk Alat Tes: ') }}{{ $AlatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ========== SECTION 1: INSTRUKSI TES ========== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        📋 Instruksi Tes
                    </h3>
                    @if ($AlatTes->instructions)
                        <button id="edit-instructions-btn" type="button"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit Instruksi
                        </button>
                    @endif
                </div>

                @if ($AlatTes->instructions)
                    <div id="instructions-display">
                        <div
                            class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg whitespace-pre-line text-sm text-gray-700">
                            {{ $AlatTes->instructions }}
                        </div>
                        <p class="text-xs text-gray-500 mt-3 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Instruksi ini akan ditampilkan kepada peserta sebelum memulai tes
                        </p>
                    </div>

                    <div id="instructions-form" class="hidden mt-4">
                        @php
                            $instrRoute = route('admin.alat-tes.questions.update_instructions', $AlatTes->id);
                        @endphp
                        <form method="POST" action="{{ $instrRoute }}">
                            @csrf
                            @method('PUT')
                            <textarea name="instructions" rows="8"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan instruksi untuk peserta...">{{ old('instructions', $AlatTes->instructions) }}</textarea>
                            <div class="flex justify-end gap-3 mt-3">
                                <button type="button" id="cancel-instructions"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Instruksi
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div id="instructions-empty">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-yellow-900">Instruksi Tes Belum Diisi</h3>
                                    <p class="text-xs text-yellow-800 mt-1">
                                        Instruksi akan membantu peserta memahami cara mengerjakan tes ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button id="show-instructions-form" type="button"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Instruksi
                        </button>
                    </div>

                    <div id="instructions-add-form" class="hidden mt-4">
                        @php
                            $instrRoute = route('admin.alat-tes.questions.update_instructions', $AlatTes->id);
                        @endphp
                        <form method="POST" action="{{ $instrRoute }}">
                            @csrf
                            @method('PUT')
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instruksi Tes</label>
                            <textarea name="instructions" rows="8"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh:&#10;1. Bacalah setiap pertanyaan dengan teliti&#10;2. Pilih jawaban yang paling sesuai&#10;3. Waktu pengerjaan akan dimulai setelah Anda klik 'Mulai'&#10;4. Pastikan koneksi internet stabil">{{ old('instructions') }}</textarea>
                            <div class="flex justify-end gap-3 mt-3">
                                <button type="button" id="cancel-add-instructions"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Instruksi
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>


            {{-- ========== SECTION 2: CONTOH SOAL DENGAN DROPDOWN ========== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    📚 Contoh Soal <span class="text-gray-500 text-sm font-normal">(Maksimal 2 contoh)</span>
                </h3>

                @php
                    $raw_examples = $AlatTes->example_questions;
                    // Pastikan $examples selalu array, meskipun dari DB isinya string JSON atau null
                    $examples = is_string($raw_examples) ? json_decode($raw_examples, true) : $raw_examples ?? [];
                    if (is_null($examples)) {
                        $examples = [];
                    }
                @endphp

                {{-- ✅ DAFTAR CONTOH SOAL YANG SUDAH ADA --}}
                @if (count($examples) > 0)
                    <ul class="space-y-3 mb-6">
                        @foreach ($examples as $idx => $ex)
                            <li class="flex items-start justify-between p-4 rounded-lg border bg-gray-50 flex-col">
                                <div class="w-full flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                class="text-sm font-semibold text-gray-800">{{ $ex['type'] ?? 'UNKNOWN' }}</span>
                                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">
                                                Contoh #{{ $idx + 1 }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            @if (($ex['type'] ?? '') === 'PAPIKOSTICK')
                                                <strong>A:</strong> {{ $ex['statement_a'] ?? '' }}<br>
                                                <strong>B:</strong> {{ $ex['statement_b'] ?? '' }}
                                            @else
                                                {{ $ex['question'] ?? 'Tidak ada pertanyaan' }}
                                            @endif
                                        </div>
                                        @if (!empty($ex['options']))
                                            <div class="text-xs text-gray-600 mt-2">
                                                <strong>Pilihan:</strong>
                                                {{ implode(', ', array_slice($ex['options'], 0, 3)) }}{{ count($ex['options']) > 3 ? '...' : '' }}
                                            </div>
                                        @endif
                                        @if (!empty($ex['explanation']))
                                            <div class="text-xs text-blue-600 mt-1 italic">
                                                💡 {{ $ex['explanation'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex-shrink-0 flex flex-col items-end gap-2">
                                        <button type="button" onclick="toggleEditExample({{ $idx }})"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>

                                        @php
                                            $destroyRoute = null;
                                            if (Route::has('admin.alat-tes.example-questions.destroy')) {
                                                $destroyRoute = route('admin.alat-tes.example-questions.destroy', [
                                                    $AlatTes->id,
                                                    $idx,
                                                ]);
                                            } elseif (Route::has('alat-tes.example-questions.destroy')) {
                                                $destroyRoute = route('alat-tes.example-questions.destroy', [
                                                    $AlatTes->id,
                                                    $idx,
                                                ]);
                                            }
                                        @endphp

                                        @if ($destroyRoute)
                                            <form method="POST" action="{{ $destroyRoute }}"
                                                onsubmit="return confirm('⚠️ Yakin ingin menghapus contoh soal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium transition flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="text-gray-400 text-sm">Hapus (route
                                                tidak tersedia)</button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Edit form (inline, hidden by default) -->
                                <div id="edit-form-{{ $idx }}"
                                    class="hidden mt-4 w-full bg-white p-4 border rounded-lg">
                                    @php
                                        $updateRoute = null;
                                        if (Route::has('admin.alat-tes.example-questions.update')) {
                                            $updateRoute = route('admin.alat-tes.example-questions.update', [
                                                $AlatTes->id,
                                                $idx,
                                            ]);
                                        } elseif (Route::has('alat-tes.example-questions.update')) {
                                            $updateRoute = route('alat-tes.example-questions.update', [
                                                $AlatTes->id,
                                                $idx,
                                            ]);
                                        }
                                    @endphp

                                    @if ($updateRoute)
                                        <form method="POST" action="{{ $updateRoute }}">
                                            @csrf
                                            @method('PUT')
                                        @else
                                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded">
                                                <p class="text-xs text-yellow-800">Rute update contoh soal tidak
                                                    tersedia. Silakan jalankan <code>php artisan route:clear</code></p>
                                            </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe
                                                Soal</label>
                                            <select id="edit_type_{{ $idx }}" name="type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <option value="">-- Pilih Jenis Soal --</option>
                                                <option value="PILIHAN_GANDA"
                                                    {{ ($ex['type'] ?? '') === 'PILIHAN_GANDA' ? 'selected' : '' }}>
                                                    Pilihan Ganda</option>
                                                <option value="PILIHAN_GANDA_KOMPLEKS"
                                                    {{ ($ex['type'] ?? '') === 'PILIHAN_GANDA_KOMPLEKS' ? 'selected' : '' }}>
                                                    Pilihan Ganda Kompleks</option>
                                                <option value="HAFALAN"
                                                    {{ ($ex['type'] ?? '') === 'HAFALAN' ? 'selected' : '' }}>Hafalan
                                                </option>
                                                <option value="PAPIKOSTICK"
                                                    {{ ($ex['type'] ?? '') === 'PAPIKOSTICK' ? 'selected' : '' }}>PAPI
                                                    Kostick</option>
                                                <option value="PAULI"
                                                    {{ ($ex['type'] ?? '') === 'PAULI' ? 'selected' : '' }}>Pauli
                                                </option>
                                                <option value="RMIB"
                                                    {{ ($ex['type'] ?? '') === 'RMIB' ? 'selected' : '' }}>RMIB
                                                </option>
                                                <option value="BINARY"
                                                    {{ ($ex['type'] ?? '') === 'BINARY' ? 'selected' : '' }}>Binary
                                                </option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm text-gray-600 mb-1">Pertanyaan / Konten</label>
                                            <input type="text" name="question"
                                                value="{{ $ex['question'] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (jika ada,
                                                pisahkan dengan enter)</label>
                                            <textarea name="options" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                placeholder="Opsi A&#10;Opsi B">{{ !empty($ex['options']) ? implode("\n", $ex['options']) : '' }}</textarea>
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Ceklis)</label>
                                            @php
                                                $correctVal = $ex['correct_answer'] ?? '';
                                                if (
                                                    empty($correctVal) &&
                                                    !empty($ex['correct_answers']) &&
                                                    is_array($ex['correct_answers'])
                                                ) {
                                                    $correctVal = implode(',', $ex['correct_answers']);
                                                }

                                                // Array jawaban benar untuk pengecekan checkbox
                                                $correctArray = [];
                                                if (isset($ex['correct_answers']) && is_array($ex['correct_answers'])) {
                                                    $correctArray = $ex['correct_answers'];
                                                } elseif (
                                                    isset($ex['correct_answer']) &&
                                                    $ex['correct_answer'] !== ''
                                                ) {
                                                    $correctArray = [$ex['correct_answer']];
                                                }
                                            @endphp

                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 border border-gray-200 rounded-lg bg-gray-50 max-h-40 overflow-y-auto">
                                                @if (!empty($ex['options']) && is_array($ex['options']))
                                                    @foreach ($ex['options'] as $optIdx => $optText)
                                                        <label
                                                            class="inline-flex items-start cursor-pointer hover:bg-gray-100 p-1 rounded">
                                                            <input type="checkbox"
                                                                class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 correct-checkbox-{{ $idx }}"
                                                                data-index="{{ $optIdx }}"
                                                                {{ in_array($optIdx, $correctArray) ? 'checked' : '' }}
                                                                onchange="updateCorrectIndices({{ $idx }})">
                                                            <span
                                                                class="ml-2 text-sm text-gray-700">{{ $optText }}</span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Simpan opsi terlebih dahulu
                                                        untuk memilih jawaban benar.</p>
                                                @endif
                                            </div>
                                            {{-- Hidden input untuk menyimpan nilai akhir (misal: "0,2") --}}
                                            <input type="hidden" name="correct"
                                                id="correct_input_{{ $idx }}" value="{{ $correctVal }}">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="explanation"
                                                value="{{ $ex['explanation'] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>

                                        <div id="edit_papi_a_{{ $idx }}" class="md:col-span-2">
                                            <label class="block text-sm text-gray-600 mb-1">Pernyataan A</label>
                                            <input type="text" name="statement_a"
                                                value="{{ $ex['statement_a'] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>

                                        <div id="edit_papi_b_{{ $idx }}" class="md:col-span-2">
                                            <label class="block text-sm text-gray-600 mb-1">Pernyataan B</label>
                                            <input type="text" name="statement_b"
                                                value="{{ $ex['statement_b'] ?? '' }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>

                                        <div id="edit_memory_type_{{ $idx }}" class="md:col-span-1">
                                            <label class="block text-sm text-gray-600 mb-1">Tipe Materi</label>
                                            <select name="memory_type"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <option value="TEXT"
                                                    {{ isset($ex['memory_type']) && $ex['memory_type'] === 'TEXT' ? 'selected' : '' }}>
                                                    TEXT</option>
                                                <option value="IMAGE"
                                                    {{ isset($ex['memory_type']) && $ex['memory_type'] === 'IMAGE' ? 'selected' : '' }}>
                                                    IMAGE</option>
                                            </select>
                                        </div>

                                        <div id="edit_duration_{{ $idx }}" class="md:col-span-1">
                                            <label class="block text-sm text-gray-600 mb-1">Durasi (detik)</label>
                                            <input type="number" name="duration_seconds"
                                                value="{{ $ex['duration_seconds'] ?? '' }}" min="1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>

                                    </div>

                                    <div class="flex justify-end gap-2 mt-3">
                                        <button type="button" onclick="toggleEditExample({{ $idx }})"
                                            class="px-3 py-2 bg-gray-200 rounded">Batal</button>
                                        @if ($updateRoute)
                                            <button type="submit"
                                                class="px-3 py-2 bg-green-600 text-white rounded">Simpan</button>
                                        @else
                                            <button type="button" disabled
                                                class="px-3 py-2 bg-gray-300 text-gray-600 rounded">Simpan (route tidak
                                                tersedia)</button>
                                        @endif
                                    </div>
                                    @if ($updateRoute)
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Belum ada contoh soal</p>
                                <p class="text-xs text-yellow-700 mt-1">Tambahkan contoh soal untuk membantu peserta
                                    memahami format tes.</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ✅ FORM TAMBAH CONTOH SOAL DENGAN DROPDOWN --}}
                @if (count($examples) < 2)
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Tambah Contoh Soal #{{ count($examples) + 1 }}
                    </h4>

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        @php
                            $storeRoute = null;
                            if (Route::has('admin.alat-tes.example-questions.store')) {
                                $storeRoute = route('admin.alat-tes.example-questions.store', $AlatTes->id);
                            } elseif (Route::has('alat-tes.example-questions.store')) {
                                $storeRoute = route('alat-tes.example-questions.store', $AlatTes->id);
                            }
                        @endphp

                        @if ($storeRoute)
                            <form method="POST" action="{{ $storeRoute }}" id="exampleQuestionForm">
                                @csrf
                            @else
                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded mb-4">
                                    <p class="text-xs text-yellow-800">Rute untuk menambah contoh soal belum tersedia.
                                        Silakan jalankan <code>php artisan route:clear</code> lalu muat ulang.</p>
                                </div>
                        @endif

                        {{-- Dropdown Pilih Jenis Soal --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Jenis Soal <span class="text-red-500">*</span>
                            </label>
                            <select id="example_type" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">-- Pilih Jenis Soal --</option>
                                <option value="PILIHAN_GANDA">📝 Pilihan Ganda (1 Jawaban)</option>
                                <option value="PILIHAN_GANDA_KOMPLEKS">📋 Pilihan Ganda (2+ Jawaban)</option>
                                <option value="HAFALAN">🧠 Hafalan (Memory Test)</option>
                                <option value="PAPIKOSTICK">👤 PAPI Kostick (Personality)</option>
                                <option value="PAULI">🔢 Pauli Test (Numerical)</option>
                                <option value="RMIB">🎓 RMIB (Interest)</option>
                                <option value="BINARY">✅ Binary (2 Jawaban)</option>
                            </select>
                        </div>

                        {{-- ✅ TEMPLATE: PILIHAN GANDA (1 Jawaban) --}}
                        <div id="template_PILIHAN_GANDA" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="question" value="Siapa presiden pertama Indonesia?"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Masukkan pertanyaan...">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan dengan
                                    enter)</label>
                                <textarea name="options" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Ir. Soekarno&#10;Mohammad Hatta&#10;Soeharto">Ir. Soekarno
Mohammad Hatta
Soeharto
BJ Habibie
Megawati</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index:
                                        0-4)</label>
                                    <input type="number" name="correct" value="0" min="0"
                                        max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">0 = Pilihan pertama</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                    <input type="text" name="explanation" value="Presiden pertama RI"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        {{-- ✅ TEMPLATE: PILIHAN GANDA KOMPLEKS (2+ Jawaban) --}}
                        <div id="template_PILIHAN_GANDA_KOMPLEKS" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                <input type="text" name="question" value="Pilih warna yang termasuk warna primer"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                <textarea name="options" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Merah
Hijau
Biru
Kuning
Hitam</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (comma-separated:
                                        0,2)</label>
                                    <input type="text" name="correct_multiple" value="0,2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                    <input type="text" name="explanation" value="Contoh warna primer"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        {{-- ✅ TEMPLATE: HAFALAN (Memory) --}}
                        <div id="template_HAFALAN" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Materi Hafalan</label>
                                <textarea name="memory_content" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">BUNGA: Dahlia, Melati, Anggrek, Mawar, Tulip</textarea>
                            </div>
                            <div class="mb-3 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Tipe Materi</label>
                                    <select name="memory_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="TEXT">TEXT</option>
                                        <option value="IMAGE">IMAGE</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Durasi (detik)</label>
                                    <input type="number" name="duration_seconds" value="10" min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                <input type="text" name="question" value="Sebutkan jenis bunga yang Anda hafal!"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                <textarea name="options" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Dahlia, Melati, Anggrek
Kamboja, Flamboyan, Soka</textarea>
                            </div>
                        </div>

                        {{-- ✅ TEMPLATE: PAPIKOSTICK --}}
                        <div id="template_PAPIKOSTICK" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pernyataan A</label>
                                <input type="text" name="statement_a"
                                    value="Saya lebih suka bekerja dalam tim daripada sendiri"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pernyataan B</label>
                                <input type="text" name="statement_b"
                                    value="Saya lebih suka bekerja sendiri daripada dalam tim"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            {{-- <div class="mb-3">
                                    <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                    <input type="text" name="explanation" value="Menunjukkan preferensi kerja tim"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div> --}}
                            <p class="mt-2 text-sm text-gray-500">💡 Tidak ada jawaban benar atau salah. Pilih A
                                atau B yang paling menggambarkan diri Anda.</p>
                        </div>

                        {{-- ✅ TEMPLATE: PAULI --}}
                        <div id="template_PAULI" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                <input type="text" name="question" value="15 + 23 = ?"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                <textarea name="options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">35
36
38
39
40</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                    <input type="number" name="correct" value="2" min="0"
                                        max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                    <input type="text" name="explanation" value="15 + 23 = 38"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        {{-- ✅ TEMPLATE: RMIB --}}
                        <div id="template_RMIB" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Instruksi</label>
                                <textarea name="instruction" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Pilih ranking 1 (paling Anda sukai) sampai 12 (paling tidak disukai) untuk setiap profesi.</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Daftar Profesi/Aktivitas (12 item,
                                    pisahkan dengan baris baru)</label>
                                <textarea name="professions" rows="12" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Masukkan 12 profesi/aktivitas...">Koreografer
Teller
Auditor
Penulis Buku
Operator Mesin
Ahli Gizi
Penghantar Musik
Fasilitator Outbound
Customer Care
Administrator Kantor
Tukang Kayu
Konsultan Pendidikan</textarea>
                                <small class="text-gray-500">Pastikan ada tepat 12 profesi/aktivitas (satu per
                                    baris)</small>
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Kode Profesi (opsional, untuk
                                    kategorisasi)</label>
                                <input type="text" name="codes" value="A,B,C,D,E,F,G,H,I,J,K,L"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <small class="text-gray-500">Pisahkan dengan koma, sesuai urutan profesi di
                                    atas</small>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Catatan:</strong> Tes RMIB tidak memiliki "jawaban benar". Peserta akan
                                    mengurutkan preferensi mereka dari ranking 1 (paling disukai) hingga 12 (paling
                                    tidak disukai).
                                </p>
                            </div>
                        </div>

                        {{-- ✅ TEMPLATE: BINARY (2 Jawaban) --}}
                        <div id="template_BINARY" class="template-content hidden">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                <input type="text" name="question"
                                    value="Apakah Anda lebih suka bekerja di pagi hari?"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                <textarea name="options" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">Ya
Tidak</textarea>
                                <p class="mt-1 text-xs text-gray-500">✅ Format 2 jawaban: Ya/Tidak, Benar/Salah,
                                    Setuju/Tidak Setuju</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index:
                                        0-1)</label>
                                    <input type="number" name="correct" value="0" min="0"
                                        max="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">0 = Ya, 1 = Tidak</p>
                                </div>
                                {{-- <div>
                                    <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                    <input type="text" name="explanation"
                                        value="Menunjukkan preferensi waktu kerja optimal"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div> --}}
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="reset" onclick="resetExampleForm()"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                                Reset
                            </button>
                            @if ($storeRoute)
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Contoh
                                </button>
                            @else
                                <button type="button" disabled
                                    class="px-4 py-2 bg-gray-300 text-gray-600 rounded-lg">Tambah Contoh (route tidak
                                    tersedia)</button>
                            @endif
                        </div>
                        @if ($storeRoute)
                            </form>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600">✅ Maksimal 2 contoh soal sudah tercapai.</p>
                    </div>
                @endif
            </div>

            {{-- ========== SECTION 3: TAMBAH SOAL BARU ========== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    Tambah Soal Baru
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.alat-tes.questions.create', $AlatTes->id) }}"
                        class="group flex flex-col gap-3 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border-2 border-blue-300 hover:border-blue-400 rounded-lg p-4 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-10 h-10 text-blue-600 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <div>
                            <p class="font-semibold text-blue-900">📝 Soal Umum</p>
                            <p class="text-xs text-blue-700 mt-1">Pilihan Ganda, Essay, Hafalan</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.alat-tes.questions.papi.create', $AlatTes->id) }}"
                        class="group flex flex-col gap-3 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border-2 border-purple-300 hover:border-purple-400 rounded-lg p-4 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-10 h-10 text-purple-600 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        <div>
                            <p class="font-semibold text-purple-900">🔷 PAPI Kostick</p>
                            <p class="text-xs text-purple-700 mt-1">90 Pasangan Pernyataan</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.alat-tes.questions.rmib.create', $AlatTes->id) }}"
                        class="group flex flex-col gap-3 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border-2 border-green-300 hover:border-green-400 rounded-lg p-4 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-10 h-10 text-green-600 group-hover:scale-110 transition-transform"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                            </path>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-900">🎓 RMIB</p>
                            <p class="text-xs text-green-700 mt-1">144 Item Minat Karir</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.pauli.create', ['alat_tes_id' => $AlatTes->id]) }}"
                        class="group flex flex-col gap-3 bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 border-2 border-orange-300 hover:border-orange-400 rounded-lg p-4 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-10 h-10 text-orange-600 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-orange-900">🔢 Pauli Test</p>
                            <p class="text-xs text-orange-700 mt-1">Kecepatan & Ketepatan</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ========== SECTION 4: DAFTAR SOAL ========== --}}
            @if ($questions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            📝 Daftar Soal Umum
                        </span>
                        <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                            {{ $questions->total() }} soal
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pertanyaan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($questions as $index => $question)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $questions->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full font-semibold
                                                {{ $question->type === 'PILIHAN_GANDA' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $question->type === 'PILIHAN_GANDA_KOMPLEKS' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $question->type === 'ESSAY' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $question->type === 'HAFALAN' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                {{ str_replace('_', ' ', $question->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="max-w-md">
                                                {{ Str::limit($question->question_text ?? ($question->example_question ?? 'Tanpa teks'), 80) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($question->ranking_category)
                                                <span
                                                    class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800 font-medium">
                                                    {{ $question->ranking_category }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.alat-tes.questions.edit', [$AlatTes->id, $question->id]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium transition">Edit</a>
                                                <form
                                                    action="{{ route('admin.alat-tes.questions.destroy', [$AlatTes->id, $question->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus soal ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium transition">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $questions->links() }}</div>
                </div>
            @endif

            @if ($papiQuestions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            🔷 Daftar Soal PAPI Kostick
                        </span>
                        <span class="text-sm bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                            {{ $papiQuestions->total() }} soal
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Pernyataan A</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Pernyataan B</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($papiQuestions as $papi)
                                    @php
                                        $meta = $papi->metadata ?? [];
                                        $statementA = $meta['statement_a'] ?? 'N/A';
                                        $statementB = $meta['statement_b'] ?? 'N/A';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-700">
                                            #{{ $papi->ranking_weight ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ Str::limit($statementA, 60) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ Str::limit($statementB, 60) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.alat-tes.questions.papi.edit', [$AlatTes->id, $papi->id]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                                <form
                                                    action="{{ route('admin.alat-tes.questions.papi.destroy', [$AlatTes->id, $papi->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $papiQuestions->links() }}</div>
                </div>
            @endif

            @if ($rmibQuestions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z">
                                </path>
                            </svg>
                            🎓 Daftar Soal RMIB
                        </span>
                        <span class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded-full">
                            {{ $rmibQuestions->total() }} soal
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rmibQuestions as $rmib)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-700">
                                            #{{ $rmib->ranking_weight ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ Str::limit($rmib->question_text, 70) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($rmib->ranking_category)
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">
                                                    {{ str_replace('_', ' ', $rmib->ranking_category) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.alat-tes.questions.rmib.edit', [$AlatTes->id, $rmib->id]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                                <form
                                                    action="{{ route('admin.alat-tes.questions.rmib.destroy', [$AlatTes->id, $rmib->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $rmibQuestions->links() }}</div>
                </div>
            @endif

            @if ($questions->count() == 0 && $papiQuestions->count() == 0 && $rmibQuestions->count() == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada soal</h3>
                        <p class="text-gray-500 mb-6">Klik salah satu tombol di atas untuk mulai membuat soal</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        // ========== MAKE FUNCTIONS GLOBAL FOR ONCLICK HANDLERS ==========
        window.toggleEditExample = function(idx) {
            const el = document.getElementById('edit-form-' + idx);
            if (!el) {
                console.error('Edit form not found for index:', idx);
                return;
            }
            el.classList.toggle('hidden');
            // Show/hide relevant fields based on type
            if (!el.classList.contains('hidden')) {
                updateEditFormVisibility(idx);
            }
        };

        function updateEditFormVisibility(idx) {
            const typeEl = document.getElementById('edit_type_' + idx);
            if (!typeEl) {
                console.error('Type select not found for index:', idx);
                return;
            }

            const type = typeEl.value;
            const papiA = document.getElementById('edit_papi_a_' + idx);
            const papiB = document.getElementById('edit_papi_b_' + idx);
            const memoryType = document.getElementById('edit_memory_type_' + idx);
            const duration = document.getElementById('edit_duration_' + idx);

            // Hide all conditional fields first
            if (papiA) papiA.style.display = 'none';
            if (papiB) papiB.style.display = 'none';
            if (memoryType) memoryType.style.display = 'none';
            if (duration) duration.style.display = 'none';

            // Show fields based on type
            if (type === 'PAPIKOSTICK') {
                if (papiA) papiA.style.display = '';
                if (papiB) papiB.style.display = '';
            } else if (type === 'HAFALAN') {
                if (memoryType) memoryType.style.display = '';
                if (duration) duration.style.display = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing scripts');

            // Helper: Disable/Enable inputs inside a container
            function toggleInputs(container, enable) {
                const inputs = container.querySelectorAll('input, select, textarea');
                inputs.forEach(el => {
                    el.disabled = !enable;
                });
            }

            // Init: Disable all inputs in templates by default
            document.querySelectorAll('.template-content').forEach(t => toggleInputs(t, false));

            // ========== DROPDOWN TEMPLATE HANDLER FOR NEW EXAMPLE ==========
            const dropdown = document.getElementById('example_type');
            if (dropdown) {
                console.log('Example type dropdown found');
                dropdown.addEventListener('change', function() {
                    const selectedType = this.value;
                    console.log('Selected type:', selectedType);

                    const templates = document.querySelectorAll('.template-content');

                    // Hide all templates
                    // Hide all templates and disable inputs
                    templates.forEach(template => {
                        template.classList.add('hidden');
                        toggleInputs(template, false);
                    });

                    // Show selected template
                    // Show selected template and enable inputs
                    if (selectedType) {
                        const targetTemplate = document.getElementById('template_' + selectedType);
                        if (targetTemplate) {
                            targetTemplate.classList.remove('hidden');
                            toggleInputs(targetTemplate, true);
                            console.log('Showing template:', 'template_' + selectedType);
                        } else {
                            console.warn('Template not found:', 'template_' + selectedType);
                        }
                    }
                });

                // Trigger change event on load to handle browser auto-fill/refresh state
                if (dropdown.value) {
                    dropdown.dispatchEvent(new Event('change'));
                }
            } else {
                console.log('Example type dropdown not found');
            }

            // ========== TOGGLE EDIT INSTRUCTIONS (when instructions exist) ==========
            const editBtn = document.getElementById('edit-instructions-btn');
            const cancelBtn = document.getElementById('cancel-instructions');
            const displayDiv = document.getElementById('instructions-display');
            const formDiv = document.getElementById('instructions-form');

            if (editBtn && displayDiv && formDiv) {
                console.log('Instructions edit elements found');
                editBtn.addEventListener('click', function() {
                    displayDiv.classList.add('hidden');
                    formDiv.classList.remove('hidden');
                });

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function() {
                        formDiv.classList.add('hidden');
                        displayDiv.classList.remove('hidden');
                    });
                }
            }

            // ========== TOGGLE ADD INSTRUCTIONS (when no instructions exist) ==========
            const showFormBtn = document.getElementById('show-instructions-form');
            const cancelAddBtn = document.getElementById('cancel-add-instructions');
            const emptyDiv = document.getElementById('instructions-empty');
            const addFormDiv = document.getElementById('instructions-add-form');

            if (showFormBtn && emptyDiv && addFormDiv) {
                console.log('Add instructions elements found');
                showFormBtn.addEventListener('click', function() {
                    emptyDiv.classList.add('hidden');
                    addFormDiv.classList.remove('hidden');
                });

                if (cancelAddBtn) {
                    cancelAddBtn.addEventListener('click', function() {
                        addFormDiv.classList.add('hidden');
                        emptyDiv.classList.remove('hidden');
                    });
                }
            }

            // ========== INITIALIZE EDIT FORM TYPE LISTENERS ==========
            @foreach ($examples as $idx => $ex)
                (function() {
                    const sel = document.getElementById('edit_type_' + {{ $idx }});
                    if (sel) {
                        console.log('Setting up edit listener for example {{ $idx }}');
                        sel.addEventListener('change', function() {
                            updateEditFormVisibility({{ $idx }});
                        });
                        // Set initial visibility
                        updateEditFormVisibility({{ $idx }});
                    } else {
                        console.warn('Edit type select not found for index {{ $idx }}');
                    }
                })();
            @endforeach

            // ========== RESET & SUBMIT HANDLERS FOR ADD EXAMPLE FORM ==========
            (function() {
                const exampleForm = document.getElementById('exampleQuestionForm');
                if (!exampleForm) {
                    console.log('Example form not present (store route may be missing)');
                    return;
                }

                const resetBtn = exampleForm.querySelector('button[type="reset"]');
                const submitBtn = exampleForm.querySelector('button[type="submit"]');

                if (resetBtn) {
                    resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        exampleForm.reset();
                        // hide any template content and clear dropdown
                        const templates = document.querySelectorAll('.template-content');
                        templates.forEach(t => t.classList.add('hidden'));
                        templates.forEach(t => {
                            t.classList.add('hidden');
                            toggleInputs(t, false);
                        });
                        const dd = document.getElementById('example_type');
                        if (dd) dd.value = '';
                        console.log('Example form reset via JS');
                    });
                }

                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        const dd = document.getElementById('example_type');
                        if (dd && dd.value === '') {
                            e.preventDefault();
                            alert('Silakan pilih jenis soal terlebih dahulu.');
                            dd.focus();
                            return;
                        }
                        // allow native submission but disable button to prevent double submit
                        submitBtn.disabled = true;
                        exampleForm.submit();
                    });
                }
            })();

            // ========== UPDATE CORRECT INDICES (CHECKBOX TO HIDDEN INPUT) ==========
            window.updateCorrectIndices = function(idx) {
                const container = document.getElementById('edit-form-' + idx);
                if (!container) return;

                const checkboxes = container.querySelectorAll('.correct-checkbox-' + idx + ':checked');
                const indices = Array.from(checkboxes).map(cb => cb.getAttribute('data-index'));

                const hiddenInput = document.getElementById('correct_input_' + idx);
                if (hiddenInput) {
                    hiddenInput.value = indices.join(',');
                }
            };

            console.log('All event listeners initialized');
        });
    </script>
</x-admin-layout>
