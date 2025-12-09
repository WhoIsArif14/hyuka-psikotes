<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pauli Test untuk: ') }}{{ $alatTes->nama }}
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

            {{-- ✅ INSTRUKSI TES (JIKA ADA) --}}
            @if($alatTes->instructions)
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
                                <a href="{{ route('admin.alat-tes.edit', $alatTes->id) }}" 
                                   class="text-xs font-normal text-blue-600 hover:text-blue-800 hover:underline ml-auto">
                                    ✏️ Edit Instruksi
                                </a>
                            </h3>
                            <div class="text-sm text-blue-900 leading-relaxed whitespace-pre-line bg-white p-4 rounded border border-blue-200">
                                {{ $alatTes->instructions }}
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
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-yellow-900">Instruksi Tes Belum Diisi</h3>
                            <p class="text-xs text-yellow-800 mt-1">
                                <a href="{{ route('admin.alat-tes.edit', $alatTes->id) }}" 
                                   class="font-semibold underline hover:text-yellow-900">
                                    Klik di sini untuk menambahkan instruksi
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ TOMBOL TAMBAH TEST PAULI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Konfigurasi Pauli Test
                </h3>

                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('admin.pauli.create', ['alat_tes_id' => $alatTes->id]) }}"
                        class="group flex items-center gap-4 bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 border-2 border-orange-300 hover:border-orange-400 rounded-lg p-5 transition-all duration-200 shadow-sm hover:shadow-md">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-orange-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-orange-900 text-lg">🔢 Pauli Test</p>
                            <p class="text-xs text-orange-700 mt-1">Tes kecepatan dan akurasi penjumlahan angka</p>
                            <p class="text-xs text-orange-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Klik untuk membuat konfigurasi test
                            </p>
                        </div>
                    </a>
                </div>

                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-800">
                        <strong>💡 Tentang Pauli Test:</strong> Test ini mengukur kecepatan kerja, ketepatan, dan konsistensi. 
                        Peserta akan menjumlahkan pasangan angka dalam kolom-kolom dengan batasan waktu.
                    </p>
                </div>
            </div>

            {{-- ✅ DAFTAR KONFIGURASI PAULI TEST --}}
            @if ($pauliTests->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        🔢 Daftar Konfigurasi Pauli Test
                        <span class="ml-auto text-sm bg-orange-100 text-orange-800 px-3 py-1 rounded-full">
                            {{ $pauliTests->total() }} konfigurasi
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konfigurasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Soal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pauliTests as $test)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-orange-700">
                                            #{{ $test->id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium">{{ $test->total_columns }} kolom</span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $test->pairs_per_column }} pasangan/kolom
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $test->time_per_column }}s per kolom
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                {{ $test->total_columns * $test->pairs_per_column }} soal
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ round(($test->total_columns * $test->time_per_column) / 60) }} menit
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($test->results->count() > 0)
                                                <a href="{{ route('admin.pauli.results', $test->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    {{ $test->results->count() }} peserta
                                                </a>
                                            @else
                                                <span class="text-gray-400">Belum ada</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $test->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                @if($test->results->count() > 0)
                                                    <a href="{{ route('admin.pauli.results', $test->id) }}" 
                                                       class="text-blue-600 hover:text-blue-800 font-medium transition">
                                                        Hasil
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.pauli.edit', $test->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 font-medium transition">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.pauli.destroy', $test->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('⚠️ Yakin ingin menghapus konfigurasi Pauli Test ini? Semua data hasil test akan ikut terhapus!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 font-medium transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pauliTests->links() }}
                    </div>
                </div>
            @endif

            {{-- ✅ JIKA TIDAK ADA KONFIGURASI --}}
            @if ($pauliTests->count() == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada konfigurasi Pauli Test</h3>
                        <p class="text-gray-500 mb-6">Buat konfigurasi test untuk mulai menggunakan Pauli Test</p>
                        <a href="{{ route('admin.pauli.create', ['alat_tes_id' => $alatTes->id]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Konfigurasi Pauli Test
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>