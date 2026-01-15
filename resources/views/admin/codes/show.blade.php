<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Kode Aktivasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <a href="{{ route('admin.codes.index') }}"
                        class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4"
                        role="alert">
                        <p class="font-bold">Sukses!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800">Informasi Batch</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Nama Batch:</span>
                            <p class="text-gray-900 font-semibold">{{ $code->batch_name ?? 'Batch ' . $code->id }}</p>

                            {{-- Form edit nama batch --}}
                            <form method="POST" action="{{ route('admin.codes.updateName', $code->id) }}"
                                class="mt-2 flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="batch_name"
                                    value="{{ old('batch_name', $code->batch_name ?? 'Batch ' . $code->id) }}"
                                    class="rounded border-gray-300 px-2 py-1" />
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
                            </form>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Nama Modul:</span>
                            <p class="text-gray-900 font-semibold">{{ $code->test->title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Jumlah Kode:</span>
                            <p class="text-gray-900 font-semibold">{{ $batchCodes->count() }} kode</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Tanggal Generate:</span>
                            <p class="text-gray-900 font-semibold">{{ $code->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Kode Digunakan:</span>
                            <p class="text-gray-900 font-semibold">
                                {{ $batchCodes->where('status', 'Used')->count() }} / {{ $batchCodes->count() }}
                                <span class="text-sm text-gray-500">
                                    ({{ $batchCodes->count() > 0 ? round(($batchCodes->where('status', 'Used')->count() / $batchCodes->count()) * 100, 1) : 0 }}%)
                                </span>
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 font-medium">Kode Tersisa:</span>
                            <p class="text-gray-900 font-semibold">
                                {{ $batchCodes->where('status', 'Pending')->count() }} kode</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Kode Aktivasi</h3>
                    <span class="text-sm text-gray-600">Total: {{ $batchCodes->count() }} kode</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                    No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                    Kode Aktivasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                    Nama Pengguna</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                    Tanggal Digunakan</th>

                                {{-- ✅ TAMBAH HEADER KOLOM TRACK --}}
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Track
                                    Pengerjaan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($batchCodes as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm border-r text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-mono font-semibold border-r text-blue-600">
                                        {{ $item->code }}</td>
                                    <td class="px-4 py-3 text-sm border-r">
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full
                                        @if ($item->status == 'Used' || $item->status == 'Completed') bg-green-100 text-green-800
                                        @elseif($item->status == 'Active' || $item->status == 'On Progress') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $item->status ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm border-r">
                                        @if ($item->user)
                                            <div>
                                                <p class="font-medium">{{ $item->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->user->email }}</p>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm border-r">
                                        {{ $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d-m-Y H:i') : '-' }}
                                    </td>

                                    {{-- ✅ TRACK PENGERJAAN --}}
                                    <td class="px-4 py-3 text-sm text-gray-800">
                                        @if ($item->progress && $item->progress_percentage > 0)
                                            {{-- Ada progress data --}}
                                            <div class="text-xs font-medium text-gray-600 truncate"
                                                title="{{ $item->progress_text }}">
                                                {{ $item->progress_text }} ({{ $item->progress_percentage }}%)
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div class="bg-blue-600 h-1.5 rounded-full"
                                                    style="width: {{ $item->progress_percentage }}%"></div>
                                            </div>
                                            @if ($item->progress->current_module)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Saat ini: {{ $item->progress->current_module }}
                                                </div>
                                            @endif
                                        @elseif($item->status === 'Completed')
                                            <span class="text-green-600 font-semibold text-xs">Test Selesai (100%)</span>
                                        @elseif($item->user)
                                            <span class="text-gray-400 text-xs">{{ $item->progress_text }}</span>
                                        @else
                                            <span class="text-gray-400 text-xs">Belum Aktif</span>
                                        @endif

                                        {{-- Reset satu kode --}}
                                        @if($item->user)
                                        <form method="POST" action="{{ route('admin.codes.reset', $item->id) }}"
                                            class="inline-block mt-2"
                                            onsubmit="return confirm('Reset kode ini? Aksi ini akan mengosongkan user & status akan kembali ke Pending.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-sm px-2 py-1 bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200">Reset</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <a href="{{ route('admin.codes.export', ['code' => $code->id, 'batch' => request('batch')]) }}"
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center justify-center transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export ke Excel
                    </a>
                    <a href="{{ route('admin.codes.index') }}"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center justify-center transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
