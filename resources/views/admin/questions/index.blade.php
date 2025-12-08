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

            {{-- Error Message --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info Message --}}
            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                    {{ session('info') }}
                </div>
            @endif

            {{-- ✅ INSTRUKSI TES (JIKA ADA) --}}
            @if($AlatTes->instructions)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg shadow-md">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                                📋 Instruksi Tes
                                <a href="{{ route('admin.alat-tes.edit', $AlatTes->id) }}" 
                                   class="text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline ml-auto">
                                    ✏️ Edit Instruksi
                                </a>
                            </h3>
                            <div class="text-sm text-blue-900 leading-relaxed whitespace-pre-line bg-white p-4 rounded border border-blue-200">
                                {{ $AlatTes->instructions }}
                            </div>
                            <p class="text-xs text-blue-700 mt-3 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Instruksi ini akan ditampilkan kepada peserta sebelum memulai tes
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Jika instruksi belum diisi --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-yellow-900">Instruksi Tes Belum Diisi</h3>
                            <p class="text-xs text-yellow-800 mt-1">
                                Instruksi akan membantu peserta memahami cara mengerjakan tes ini. 
                                <a href="{{ route('admin.alat-tes.edit', $AlatTes->id) }}" 
                                   class="font-semibold underline hover:text-yellow-900">
                                    Klik di sini untuk menambahkan instruksi
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ TOMBOL TAMBAH SOAL BERDASARKAN TIPE --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Soal Baru
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Soal Umum (Pilihan Ganda, Essay, Hafalan) --}}
                    <a href="{{ route('admin.alat-tes.questions.create', $AlatTes->id) }}"
                        class="group flex items-center gap-4 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border-2 border-blue-300 hover:border-blue-400 rounded-lg p-5 transition-all duration-200 shadow-sm hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-blue-600 group-hover:scale-110 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-blue-900 text-lg">📝 Soal Umum</p>
                            <p class="text-xs text-blue-700 mt-1">Pilihan Ganda, Essay, Hafalan</p>
                            <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Klik untuk membuat
                            </p>
                        </div>
                    </a>

                    {{-- PAPI Kostick (90 Pasangan Pernyataan) --}}
                    <a href="{{ route('admin.alat-tes.questions.papi.create', $AlatTes->id) }}"
                        class="group flex items-center gap-4 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border-2 border-purple-300 hover:border-purple-400 rounded-lg p-5 transition-all duration-200 shadow-sm hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-purple-600 group-hover:scale-110 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-purple-900 text-lg">🔷 PAPI Kostick</p>
                            <p class="text-xs text-purple-700 mt-1">90 Pasangan Pernyataan</p>
                            <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Klik untuk membuat
                            </p>
                        </div>
                    </a>

                    {{-- RMIB (144 Item Minat Karir) --}}
                    <a href="{{ route('admin.alat-tes.questions.rmib.create', $AlatTes->id) }}"
                        class="group flex items-center gap-4 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border-2 border-green-300 hover:border-green-400 rounded-lg p-5 transition-all duration-200 shadow-sm hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-green-600 group-hover:scale-110 transition-transform"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-green-900 text-lg">🎓 RMIB</p>
                            <p class="text-xs text-green-700 mt-1">144 Item Minat Karir</p>
                            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Klik untuk membuat
                            </p>
                        </div>
                    </a>
                </div>

                {{-- Info Box --}}
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-800">
                        <strong>💡 Tips:</strong> Pilih tipe soal yang sesuai dengan kebutuhan tes Anda. Setiap tipe
                        memiliki format dan metode penilaian yang berbeda.
                    </p>
                </div>
            </div>

            {{-- ✅ DAFTAR SOAL UMUM --}}
            @if ($questions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        📝 Daftar Soal Umum
                        <span class="ml-auto text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
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
                                                @if ($question->image_path)
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span class="text-xs text-gray-500">Ada gambar</span>
                                                    </div>
                                                @endif
                                                {{ Str::limit($question->question_text ?? ($question->example_question ?? 'Tanpa teks'), 80) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($question->ranking_category)
                                                <span
                                                    class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800 font-medium">
                                                    {{ $question->ranking_category }}
                                                </span>
                                                @if ($question->ranking_weight > 1)
                                                    <span class="ml-1 text-xs text-gray-500">
                                                        (Bobot: {{ $question->ranking_weight }})
                                                    </span>
                                                @endif
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

                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ DAFTAR SOAL PAPI KOSTICK --}}
            @if ($papiQuestions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        🔷 Daftar Soal PAPI Kostick
                        <span class="ml-auto text-sm bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                            {{ $papiQuestions->total() }} soal
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pernyataan A</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pernyataan B</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kunci Scoring</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($papiQuestions as $papi)
                                    @php
                                        $options = json_decode($papi->options, true);
                                        $statementA = $options[0]['text'] ?? 'N/A';
                                        $statementB = $options[1]['text'] ?? 'N/A';

                                        // Parse scoring keys from ranking_category (format: "A:G/N|B:L/O")
                                        $scoringData = [];
                                        if ($papi->ranking_category) {
                                            $parts = explode('|', $papi->ranking_category);
                                            foreach ($parts as $part) {
                                                if (strpos($part, ':') !== false) {
                                                    [$option, $keys] = explode(':', $part);
                                                    $scoringData[$option] = $keys;
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-700">
                                            #{{ $papi->ranking_weight ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="max-w-xs">
                                                {{ Str::limit($statementA, 60) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="max-w-xs">
                                                {{ Str::limit($statementB, 60) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            @if (!empty($scoringData))
                                                @if (isset($scoringData['A']))
                                                    <div class="text-green-700 font-medium">A: {{ $scoringData['A'] }}
                                                    </div>
                                                @endif
                                                @if (isset($scoringData['B']))
                                                    <div class="text-blue-700 font-medium">B: {{ $scoringData['B'] }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded">Belum
                                                    diisi</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.alat-tes.questions.papi.edit', [$AlatTes->id, $papi->id]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium transition">Edit</a>
                                                <form
                                                    action="{{ route('admin.alat-tes.questions.papi.destroy', [$AlatTes->id, $papi->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus soal PAPI ini?');">
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

                    <div class="mt-4">
                        {{ $papiQuestions->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ DAFTAR SOAL RMIB --}}
            @if ($rmibQuestions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                            </path>
                        </svg>
                        🎓 Daftar Soal RMIB
                        <span class="ml-auto text-sm bg-green-100 text-green-800 px-3 py-1 rounded-full">
                            {{ $rmibQuestions->total() }} soal
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deskripsi Aktivitas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bidang Minat</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rating</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rmibQuestions as $rmib)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-700">
                                            #{{ $rmib->ranking_weight ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="max-w-md flex items-start gap-2">
                                                @if ($rmib->image_path)
                                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                                <span>{{ Str::limit($rmib->question_text, 70) }}</span>
                                            </div>
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
                                        <td class="px-6 py-4 text-xs text-gray-600">
                                            <div class="flex gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-gray-500">Skala 1-5</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.alat-tes.questions.rmib.edit', [$AlatTes->id, $rmib->id]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium transition">Edit</a>
                                                <form
                                                    action="{{ route('admin.alat-tes.questions.rmib.destroy', [$AlatTes->id, $rmib->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus item RMIB ini?');">
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

                    <div class="mt-4">
                        {{ $rmibQuestions->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ JIKA TIDAK ADA SOAL SAMA SEKALI --}}
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
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('admin.alat-tes.questions.create', $AlatTes->id) }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                + Soal Umum
                            </a>
                            <a href="{{ route('admin.alat-tes.questions.papi.create', $AlatTes->id) }}"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                + PAPI Kostick
                            </a>
                            <a href="{{ route('admin.alat-tes.questions.rmib.create', $AlatTes->id) }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                + RMIB
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFormBtn = document.getElementById('toggleFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const formContainer = document.getElementById('formContainer');
            const typeSelect = document.getElementById('type');
            const optionsSection = document.getElementById('options-section');
            const memoryContainer = document.getElementById('memory-container');
            const papiContainer = document.getElementById('papi-container');
            const rmibContainer = document.getElementById('rmib-scoring-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionImageContainer = document.getElementById('question-image-container');
            const addOptionBtn = document.getElementById('addOptionBtn');
            const optionsList = document.getElementById('optionsList');
            const questionImage = document.getElementById('question_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');

            const rankingCategorySelect = document.getElementById('ranking_category');
            const customCategoryInput = document.getElementById('custom-category-input');
            const customCategoryField = document.getElementById('custom_ranking_category');
            const rankingContainer = document.getElementById('ranking-category-container');

            // ✅ PAPI AUTO-GENERATE ELEMENTS
            const autoGeneratePapi = document.getElementById('auto_generate_papi');
            const papiManualInput = document.getElementById('papi-manual-input');
            const papiManualWarning = document.getElementById('papi-manual-warning');

            const MAX_FILE_SIZE = 5 * 1024 * 1024;

            // ===== TAB NAVIGATION =====
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

            let optionCount = 4;
            if (optionsList) {
                optionCount = optionsList.children.length;
            }

            // ===== IMAGE VALIDATION =====
            const validateAndPreviewImage = (imageInput, previewImg, imagePreview, removeBtn) => {
                if (!imageInput) return;

                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > MAX_FILE_SIZE) {
                            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                            alert(
                                `❌ FILE TERLALU BESAR!\n\n` +
                                `Nama file: ${file.name}\n` +
                                `Ukuran file: ${fileSizeMB} MB\n` +
                                `Maksimal: 5 MB\n\n` +
                                `Silakan pilih file yang lebih kecil atau kompres gambar terlebih dahulu.`
                            );
                            e.target.value = '';
                            if (imagePreview) imagePreview.style.display = 'none';
                            if (removeBtn) removeBtn.style.display = 'none';
                            return;
                        }

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

            if (questionImage && previewImg && imagePreview && removeImageBtn) {
                validateAndPreviewImage(questionImage, previewImg, imagePreview, removeImageBtn);
            }

            function setupOptionImagePreview(optionItem) {
                const imageInput = optionItem.querySelector('.option-image-input');
                const previewContainer = optionItem.querySelector('.option-image-preview');
                const previewImg = optionItem.querySelector('.option-preview-img');
                const removeBtn = optionItem.querySelector('.remove-option-image-btn');

                if (imageInput && previewContainer && previewImg) {
                    validateAndPreviewImage(imageInput, previewImg, previewContainer, removeBtn);
                }
            }

            document.querySelectorAll('.option-item').forEach(item => {
                setupOptionImagePreview(item);
            });

            // ===== FORM SUBMISSION VALIDATION =====
            const questionForm = document.getElementById('questionForm');
            if (questionForm) {
                questionForm.addEventListener('submit', function(e) {
                    let oversizedFiles = [];

                    const qImg = document.getElementById('question_image');
                    if (qImg && qImg.files[0]) {
                        if (qImg.files[0].size > MAX_FILE_SIZE) {
                            const sizeMB = (qImg.files[0].size / (1024 * 1024)).toFixed(2);
                            oversizedFiles.push(`• Gambar Pertanyaan: ${sizeMB} MB`);
                        }
                    }

                    document.querySelectorAll('.option-image-input').forEach((input, i) => {
                        if (input.files[0]) {
                            if (input.files[0].size > MAX_FILE_SIZE) {
                                const sizeMB = (input.files[0].size / (1024 * 1024)).toFixed(2);
                                const letter = String.fromCharCode(65 + i);
                                oversizedFiles.push(`• Gambar Opsi ${letter}: ${sizeMB} MB`);
                            }
                        }
                    });

                    if (oversizedFiles.length > 0) {
                        e.preventDefault();
                        alert(
                            `❌ TIDAK DAPAT MENYIMPAN!\n\n` +
                            `File berikut melebihi batas maksimal 5 MB:\n\n` +
                            `${oversizedFiles.join('\n')}\n\n` +
                            `Mohon ganti dengan file yang lebih kecil atau kompres gambar terlebih dahulu.`
                        );
                        formContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        return false;
                    }

                    if (rankingCategorySelect && rankingCategorySelect.value === 'CUSTOM' &&
                        customCategoryField && customCategoryField.value.trim()) {
                        const customValue = customCategoryField.value.trim().toUpperCase();
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'ranking_category';
                        hiddenInput.value = customValue;
                        questionForm.appendChild(hiddenInput);
                        rankingCategorySelect.disabled = true;
                    }
                });
            }

            // ===== FORM TOGGLE =====
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

            // ===== UPDATE OPTION LABELS =====
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
                        removeBtn.style.display = totalOptions > 2 && !isPapi && !isRMIB ? 'block' : 'none';
                    }
                });
            }

            // ===== CUSTOM CATEGORY =====
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

            // ✅ TOGGLE PAPI AUTO-GENERATE
            if (autoGeneratePapi) {
                autoGeneratePapi.addEventListener('change', function() {
                    if (this.checked) {
                        // Mode Auto-Generate
                        if (papiManualInput) papiManualInput.classList.add('hidden');
                        if (papiManualWarning) papiManualWarning.classList.add('hidden');
                        if (optionsSection) optionsSection.style.display = 'none';
                        if (questionTextContainer) questionTextContainer.style.display = 'none';
                        if (questionImageContainer) questionImageContainer.style.display = 'none';

                        const soalUtamaInfo = document.getElementById('soal-utama-info');
                        if (soalUtamaInfo) {
                            soalUtamaInfo.innerHTML = `
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-green-900 mb-1">✨ Mode Auto-Generate Aktif</h3>
                                    <p class="text-sm text-green-700">90 soal PAPI Kostick akan dibuat otomatis. Fokus pada pengisian <strong>Contoh Soal</strong> dan <strong>Instruksi</strong> di tab di bawah.</p>
                                </div>
                            </div>
                        `;
                            soalUtamaInfo.classList.remove('bg-blue-50', 'border-blue-200');
                            soalUtamaInfo.classList.add('bg-green-50', 'border-green-200');
                        }

                        const typeHint = document.getElementById('type-hint');
                        if (typeHint) {
                            typeHint.innerHTML =
                                '✅ Mode Auto-Generate: 90 soal akan dibuat otomatis. Isi <strong>Contoh Soal</strong> dan <strong>Instruksi</strong>.';
                            typeHint.classList.add('text-green-600', 'font-semibold');
                        }
                    } else {
                        // Mode Manual
                        if (papiManualInput) papiManualInput.classList.remove('hidden');
                        if (papiManualWarning) papiManualWarning.classList.remove('hidden');
                        if (optionsSection) optionsSection.style.display = 'block';
                        if (questionTextContainer) questionTextContainer.style.display = 'block';

                        const soalUtamaInfo = document.getElementById('soal-utama-info');
                        if (soalUtamaInfo) {
                            soalUtamaInfo.innerHTML = `
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-blue-900 mb-1">Soal yang akan dijawab peserta</h3>
                                    <p class="text-sm text-blue-700">Ini adalah soal aktual yang akan diberikan kepada peserta dalam tes.</p>
                                </div>
                            </div>
                        `;
                            soalUtamaInfo.classList.remove('bg-green-50', 'border-green-200');
                            soalUtamaInfo.classList.add('bg-blue-50', 'border-blue-200');
                        }

                        const typeHint = document.getElementById('type-hint');
                        if (typeHint) {
                            typeHint.innerHTML = '⚠️ Mode Manual: Anda akan membuat soal satu per satu';
                            typeHint.classList.remove('text-green-600', 'font-semibold');
                        }
                    }
                });
            }

            // ===== TOGGLE CONTAINERS =====
            function toggleContainers() {
                if (!typeSelect) return;

                const selectedType = typeSelect.value;
                const multipleAnswersInfo = document.getElementById('multiple-answers-info');
                const rmibRatingInfo = document.getElementById('rmib-rating-info');
                const typeHint = document.getElementById('type-hint');

                // Reset
                if (optionsSection) optionsSection.style.display = 'none';
                if (memoryContainer) memoryContainer.classList.add('hidden');
                if (papiContainer) papiContainer.classList.add('hidden');
                if (rmibContainer) rmibContainer.classList.add('hidden');
                if (questionTextContainer) questionTextContainer.style.display = 'block';
                if (questionImageContainer) questionImageContainer.style.display = 'block';
                if (addOptionBtn) addOptionBtn.style.display = 'block';
                if (multipleAnswersInfo) multipleAnswersInfo.classList.add('hidden');
                if (rmibRatingInfo) rmibRatingInfo.classList.add('hidden');

                if (rankingContainer) {
                    if (selectedType === 'PAPIKOSTICK') {
                        rankingContainer.classList.add('hidden');
                    } else {
                        rankingContainer.classList.remove('hidden');
                    }
                }

                const questionTextLabel = document.getElementById('question-text-label');
                const questionTextHint = document.getElementById('question-text-hint');
                const optionsHint = document.getElementById('options-hint');

                if (questionTextLabel) questionTextLabel.textContent = 'Teks Pertanyaan';
                if (questionTextHint) questionTextHint.textContent = 'Pertanyaan untuk peserta';
                if (optionsHint) optionsHint.textContent = 'Pilihan jawaban untuk pertanyaan';

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

                // RMIB HANDLING
                if (selectedType === 'RMIB') {
                    if (rmibContainer) rmibContainer.classList.remove('hidden');
                    if (optionsSection) optionsSection.style.display = 'block';
                    if (addOptionBtn) addOptionBtn.style.display = 'none';

                    if (questionTextLabel) questionTextLabel.textContent = 'Deskripsi Aktivitas/Item';
                    if (questionTextHint) questionTextHint.textContent =
                        'Jelaskan aktivitas yang akan dinilai peserta';
                    if (optionsHint) optionsHint.textContent = 'Rating scale untuk mengukur tingkat ketertarikan';

                    ensureOptionCount(5);

                    const ratingLabels = ['Sangat Tidak Suka', 'Tidak Suka', 'Netral', 'Suka', 'Sangat Suka'];

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
                                input.readOnly = true;
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
                // ✅ PAPIKOSTICK HANDLING
                else if (selectedType === 'PAPIKOSTICK') {
                    if (papiContainer) papiContainer.classList.remove('hidden');
                    if (questionImageContainer) questionImageContainer.style.display = 'none';

                    // ✅ Check auto-generate status
                    const autoGen = document.getElementById('auto_generate_papi');
                    if (autoGen && autoGen.checked) {
                        if (papiManualInput) papiManualInput.classList.add('hidden');
                        if (papiManualWarning) papiManualWarning.classList.add('hidden');
                        if (optionsSection) optionsSection.style.display = 'none';
                        if (questionTextContainer) questionTextContainer.style.display = 'none';
                    } else {
                        if (papiManualInput) papiManualInput.classList.remove('hidden');
                        if (papiManualWarning) papiManualWarning.classList.remove('hidden');
                        if (optionsSection) optionsSection.style.display = 'block';
                        if (questionTextContainer) questionTextContainer.style.display = 'block';

                        if (questionTextLabel) questionTextLabel.textContent = 'Nomor Soal PAPI (1-90)';
                        const questionTextInput = document.getElementById('question_text');
                        if (questionTextInput) questionTextInput.placeholder =
                            'Masukkan Nomor Soal (mis: 45) di sini.';
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
                }
                // ESSAY
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
                // HAFALAN
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

            function ensureOptionCount(count) {
                const currentOptions = document.querySelectorAll('.option-item');
                const currentCount = currentOptions.length;

                if (currentCount < count) {
                    for (let i = currentCount; i < count; i++) {
                        addNewOption();
                    }
                } else if (currentCount > count) {
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
                        <input type="file" name="options[${newIndex}][image_file]" accept="image/*" class="option-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <div class="option-image-preview mt-2" style="display: none;">
                            <img src="" alt="Preview Opsi" class="max-w-xs max-h-32 rounded border border-gray-300 option-preview-img">
                            <button type="button" class="remove-option-image-btn text-red-600 hover:text-red-800 text-xs font-medium mt-1">Hapus Gambar</button>
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

            if (addOptionBtn && optionsList) {
                addOptionBtn.addEventListener('click', addNewOption);
            }

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

            @if ($errors->any())
                toggleContainers();
            @endif
        });
    </script>
</x-admin-layout>
