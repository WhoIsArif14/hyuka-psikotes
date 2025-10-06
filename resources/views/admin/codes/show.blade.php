<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Kode Aktivasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Menampilkan notifikasi sukses saat batch baru dibuat --}}
            @if (session('success') && session('generated_batch_code'))
                <div class="bg-indigo-100 border-l-4 border-indigo-500 text-indigo-700 p-4" role="alert">
                    <p class="font-bold">Pembuatan Berhasil!</p>
                    <p>{{ session('success') }}</p>
                    <p class="mt-2 text-sm">Kode Batch Utama: <strong>{{ session('generated_batch_code') }}</strong> (Total {{ session('generated_quantity') }} kode unik).</p>
                </div>
            @endif
            
            {{-- Form Pembuatan Kode (Form yang Anda berikan sebelumnya, diletakkan di dalam card) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Kode Aktivasi Massal</h3>
                    <form method="POST" action="{{ route('admin.codes.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Pilih Tes --}}
                            <div class="md:col-span-2">
                                <label for="test_id" class="block text-sm font-medium text-gray-700">Pilih Tes (Modul)</label>
                                <select name="test_id" id="test_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">-- Pilih salah satu tes --</option>
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>{{ $test->title }} (Kode: {{ $test->test_code }})</option>
                                    @endforeach
                                </select>
                                @error('test_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Jumlah Kode --}}
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Kode</label>
                                <input type="number" name="quantity" id="quantity" required min="1" max="1000" value="{{ old('quantity', 50) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Buat Kode
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Data Batch Kode Aktivasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Batch Kode Aktivasi</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Kolom utama adalah Modul --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama (Modul)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Batch</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty/Digunakan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($codes as $code)
                                    <tr>
                                        {{-- 1. Nama Modul (Dapat diklik untuk Edit Modul) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.tests.edit', $code->test_id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                {{ $code->test->title ?? 'Modul Tidak Ditemukan' }}
                                            </a>
                                        </td>
                                        {{-- 2. Kode Batch Utama --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $code->batch_code }}
                                        </td>
                                        {{-- 3. Kuantitas / Digunakan --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $code->used_count }} / {{ $code->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($code->status == 'Active') bg-green-100 text-green-800 
                                                @elseif($code->status == 'Used') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $code->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $code->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Tombol Aksi: Melihat Kode Individual --}}
                                            <a href="{{ route('admin.codes.show', $code) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Lihat Kode</a>
                                            {{-- Tambahkan tombol hapus jika diperlukan --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada batch kode aktivasi yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $codes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>