<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Alat Tes: {{ $AlatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <span class="block sm:inline">Silakan periksa input Anda dan coba lagi.</span>
                            <ul class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.alat-tes.update', $AlatTes->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Alat Tes --}}
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">
                                Nama Alat Tes <span class="text-red-500">*</span>
                            </label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                type="text" name="name" value="{{ old('name', $AlatTes->name) }}" required
                                autofocus />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Durasi --}}
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">
                                Durasi (dalam Menit) <span class="text-red-500">*</span>
                            </label>
                            <input id="duration_minutes"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-500 @enderror"
                                type="number" name="duration_minutes"
                                value="{{ old('duration_minutes', $AlatTes->duration_minutes) }}" required />
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Instruksi Tes --}}
                        <div class="mt-4">
                            <label for="instructions" class="block font-medium text-sm text-gray-700">
                                Petunjuk Soal <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <textarea id="instructions" name="instructions" rows="5"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('instructions') border-red-500 @enderror"
                                placeholder="Masukkan petunjuk pengerjaan soal...">{{ old('instructions', $AlatTes->instructions) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Petunjuk ini akan ditampilkan kepada peserta sebelum memulai tes.
                            </p>
                            @error('instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ✅ SECTION: Contoh Soal --}}
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Contoh Soal <span class="text-gray-500 text-sm font-normal">(Opsional - Maksimal 2
                                    contoh)</span>
                            </h3>

                            {{-- Contoh Soal 1 --}}
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">📝 Contoh Soal 1</h4>
                                    <select id="example_type_1" name="example_1_type"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        <option value="PILIHAN_GANDA"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'PILIHAN_GANDA' ? 'selected' : '' }}>
                                            Pilihan Ganda (1 Jawaban)</option>
                                        <option value="PILIHAN_GANDA_KOMPLEKS"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'PILIHAN_GANDA_KOMPLEKS' ? 'selected' : '' }}>
                                            Pilihan Ganda (2+ Jawaban)</option>
                                        <option value="HAFALAN"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'HAFALAN' ? 'selected' : '' }}>
                                            Hafalan (Memory Test)</option>
                                        <option value="PAPIKOSTICK"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'PAPIKOSTICK' ? 'selected' : '' }}>
                                            PAPI (Personality)</option>
                                        <option value="PAULI"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'PAULI' ? 'selected' : '' }}>
                                            Pauli Test (Numerical)</option>
                                        <option value="RMIB"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'RMIB' ? 'selected' : '' }}>
                                            RMIB (Interest)</option>
                                        <option value="BINARY"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'BINARY' ? 'selected' : '' }}>
                                            Pilihan Ganda (2 Jawaban)</option>
                                        <option value="CUSTOM"
                                            {{ old('example_1_type', $examples[0]['type'] ?? '') == 'CUSTOM' ? 'selected' : '' }}>
                                            Custom (Buat Sendiri)</option>
                                    </select>
                                </div>

                                {{-- Reuse templates similar to create form but prefilled with existing example data --}}
                                <div id="template_PILIHAN_GANDA_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question"
                                            value="{{ old('example_1_question', $examples[0]['question'] ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : '') }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index:
                                                0-4)</label>
                                            <input type="number" name="example_1_correct"
                                                value="{{ old('example_1_correct', $examples[0]['correct_answer'] ?? 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation"
                                                value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                <div id="template_PILIHAN_GANDA_KOMPLEKS_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question"
                                            value="{{ old('example_1_question', $examples[0]['question'] ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Untuk jawaban benar multiple: masukkan
                                            index jawaban yang benar dipisah koma (mis: 0,2)</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Indexs:
                                                comma-separated)</label>
                                            <input type="text" name="example_1_correct_multiple"
                                                value="{{ old('example_1_correct_multiple', isset($examples[0]['correct_answers']) ? implode(',', $examples[0]['correct_answers']) : '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation"
                                                value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template PAPIKOSTICK --}}
                                <div id="template_PAPIKOSTICK_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pernyataan A</label>
                                        <input type="text" name="example_1_statement_a"
                                            value="{{ old('example_1_statement_a', $examples[0]['statement_a'] ?? 'Saya lebih suka bekerja dalam tim daripada sendiri') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pernyataan B</label>
                                        <input type="text" name="example_1_statement_b"
                                            value="{{ old('example_1_statement_b', $examples[0]['statement_b'] ?? 'Saya lebih suka bekerja sendiri daripada dalam tim') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Tidak ada jawaban benar atau salah. Pilih A
                                        atau B yang paling menggambarkan diri Anda.</p>
                                    <div class="mb-3 mt-3">
                                        <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                        <input type="text" name="example_1_explanation"
                                            value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? 'Menunjukkan preferensi kerja tim') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>

                                {{-- Template PAULI --}}
                                <div id="template_PAULI_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question"
                                            value="{{ old('example_1_question', $examples[0]['question'] ?? '15 + 23 = ?') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : "35\n36\n38\n39\n40") }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Index)</label>
                                            <input type="number" name="example_1_correct"
                                                value="{{ old('example_1_correct', $examples[0]['correct_answer'] ?? 2) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation"
                                                value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? '15 + 23 = 38') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template RMIB --}}
                                <div id="template_RMIB_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question"
                                            value="{{ old('example_1_question', $examples[0]['question'] ?? 'Pekerjaan yang paling Anda minati:') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : "Programmer\nDesainer Grafis\nGuru\nDokter\nPengusaha") }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Index)</label>
                                            <input type="number" name="example_1_correct"
                                                value="{{ old('example_1_correct', $examples[0]['correct_answer'] ?? 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation"
                                                value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? 'Minat di bidang teknologi') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                <div id="template_HAFALAN_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Materi Hafalan</label>
                                        <textarea name="example_1_memory_content" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_memory_content', $examples[0]['memory']['content'] ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Tipe Materi</label>
                                        <select name="example_1_memory_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="TEXT"
                                                {{ old('example_1_memory_type', $examples[0]['memory']['type'] ?? '') == 'TEXT' ? 'selected' : '' }}>
                                                TEXT</option>
                                            <option value="IMAGE"
                                                {{ old('example_1_memory_type', $examples[0]['memory']['type'] ?? '') == 'IMAGE' ? 'selected' : '' }}>
                                                IMAGE</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Durasi (detik)</label>
                                            <input type="number" name="example_1_duration_seconds"
                                                value="{{ old('example_1_duration_seconds', $examples[0]['memory']['duration_seconds'] ?? 10) }}"
                                                min="1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                            <input type="text" name="example_1_question"
                                                value="{{ old('example_1_question', $examples[0]['question'] ?? '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : '') }}</textarea>
                                    </div>
                                </div>

                                <div id="template_custom_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question"
                                            value="{{ old('example_1_question', $examples[0]['question'] ?? '') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Masukkan pertanyaan Anda...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan
                                            dengan enter)</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Pilihan A&#10;Pilihan B&#10;Pilihan C">{{ old('example_1_options', isset($examples[0]['options']) ? implode("\n", $examples[0]['options']) : '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Setiap baris = 1 pilihan jawaban</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index:
                                                0-4)</label>
                                            <input type="number" name="example_1_correct"
                                                value="{{ old('example_1_correct', $examples[0]['correct_answer'] ?? 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation"
                                                value="{{ old('example_1_explanation', $examples[0]['explanation'] ?? '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Contoh Soal 2 --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">📝 Contoh Soal 2</h4>
                                    <select id="example_type_2" name="example_2_type"
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        <option value="PILIHAN_GANDA"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'PILIHAN_GANDA' ? 'selected' : '' }}>
                                            Pilihan Ganda (1 Jawaban)</option>
                                        <option value="PILIHAN_GANDA_KOMPLEKS"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'PILIHAN_GANDA_KOMPLEKS' ? 'selected' : '' }}>
                                            Pilihan Ganda (2+ Jawaban)</option>
                                        <option value="HAFALAN"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'HAFALAN' ? 'selected' : '' }}>
                                            Hafalan (Memory Test)</option>
                                        <option value="PAPIKOSTICK"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'PAPIKOSTICK' ? 'selected' : '' }}>
                                            PAPI (Personality)</option>
                                        <option value="PAULI"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'PAULI' ? 'selected' : '' }}>
                                            Pauli Test (Numerical)</option>
                                        <option value="RMIB"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'RMIB' ? 'selected' : '' }}>
                                            RMIB (Interest)</option>
                                        <option value="BINARY"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'BINARY' ? 'selected' : '' }}>
                                            Pilihan Ganda (2 Jawaban)</option>
                                        <option value="CUSTOM"
                                            {{ old('example_2_type', $examples[1]['type'] ?? '') == 'CUSTOM' ? 'selected' : '' }}>
                                            Custom (Buat Sendiri)</option>
                                    </select>
                                </div>

                                {{-- Template PAPI --}}
                                <div id="template_PAPIKOSTICK_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question"
                                            value="{{ old('example_2_question', $examples[1]['question'] ?? 'Saya mudah beradaptasi dengan perubahan') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_2_options', isset($examples[1]['options']) ? implode("\n", $examples[1]['options']) : "Sangat Tidak Setuju\nTidak Setuju\nNetral\nSetuju\nSangat Setuju") }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Index)</label>
                                            <input type="number" name="example_2_correct"
                                                value="{{ old('example_2_correct', $examples[1]['correct_answer'] ?? 4) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation"
                                                value="{{ old('example_2_explanation', $examples[1]['explanation'] ?? 'Menunjukkan fleksibilitas tinggi') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Pauli --}}
                                <div id="template_PAULI_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question"
                                            value="{{ old('example_2_question', $examples[1]['question'] ?? '42 - 17 = ?') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_2_options', isset($examples[1]['options']) ? implode("\n", $examples[1]['options']) : "23\n24\n25\n26\n27") }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Index)</label>
                                            <input type="number" name="example_2_correct"
                                                value="{{ old('example_2_correct', $examples[1]['correct_answer'] ?? 2) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation"
                                                value="{{ old('example_2_explanation', $examples[1]['explanation'] ?? '42 - 17 = 25') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template RMIB --}}
                                <div id="template_RMIB_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question"
                                            value="{{ old('example_2_question', $examples[1]['question'] ?? 'Aktivitas yang paling Anda sukai:') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('example_2_options', isset($examples[1]['options']) ? implode("\n", $examples[1]['options']) : "Membaca buku\nBerolahraga\nMenggambar\nBermain musik\nMemasak") }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar
                                                (Index)</label>
                                            <input type="number" name="example_2_correct"
                                                value="{{ old('example_2_correct', $examples[1]['correct_answer'] ?? 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation"
                                                value="{{ old('example_2_explanation', $examples[1]['explanation'] ?? 'Minat pada pembelajaran') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="flex items-center justify-end mt-6 pt-6 border-t">
                                <a href="{{ route('admin.alat-tes.index') }}"
                                    class="text-gray-600 hover:text-gray-900 mr-4">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Alat Tes
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk handle dropdown (sama seperti create) --}}
    <script>
        function handleExampleDropdown(selectId, templateIds) {
            const selectEl = document.getElementById(selectId);
            if (!selectEl) return;
            selectEl.addEventListener('change', function() {
                const selectedType = this.value;
                const templates = document.querySelectorAll(templateIds);
                templates.forEach(template => template.classList.add('hidden'));
                if (selectedType) {
                    const el = document.getElementById('template_' + selectedType + '_' + selectId.split('_')
                .pop());
                    if (el) el.classList.remove('hidden');
                }
            });
        }

        // Initialize handlers
        handleExampleDropdown('example_type_1',
            '#template_PILIHAN_GANDA_1, #template_PILIHAN_GANDA_KOMPLEKS_1, #template_HAFALAN_1, #template_PAPIKOSTICK_1, #template_PAULI_1, #template_RMIB_1, #template_binary_1, #template_custom_1'
            );
        handleExampleDropdown('example_type_2',
            '#template_PILIHAN_GANDA_2, #template_PILIHAN_GANDA_KOMPLEKS_2, #template_HAFALAN_2, #template_PAPIKOSTICK_2, #template_PAULI_2, #template_RMIB_2, #template_binary_2, #template_custom_2'
            );

        // Trigger change on load to show pre-selected templates
        document.addEventListener('DOMContentLoaded', function() {
            const e1 = document.getElementById('example_type_1');
            const e2 = document.getElementById('example_type_2');
            if (e1) e1.dispatchEvent(new Event('change'));
            if (e2) e2.dispatchEvent(new Event('change'));
        });
    </script>
</x-admin-layout>
