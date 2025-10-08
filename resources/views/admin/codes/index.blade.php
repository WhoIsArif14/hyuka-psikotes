<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kode Aktivasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <p class="font-bold">Gagal!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header dengan tombol Add -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kode Aktivasi</h3>
                        <button type="button" id="toggleFormBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add
                        </button>
                    </div>

                    <!-- Form Generate Kode (Hidden by default) -->
                    <div id="formContainer" style="display: none;" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Buat Kode Aktivasi Massal</h4>
                        <form method="POST" action="{{ route('admin.codes.store') }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="batch_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Batch (Opsional)</label>
                                    <input type="text" name="batch_name" id="batch_name" placeholder="Contoh: Batch Januari 2025" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="test_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Modul Tes</label>
                                    <select name="test_id" id="test_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">-- Pilih Modul --</option>
                                        @foreach($tests as $test)
                                            <option value="{{ $test->id }}">{{ $test->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kode (Qty)</label>
                                    <input type="number" name="quantity" id="quantity" required min="1" max="1000" value="10" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg">
                                    Batal
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                                    Generate Kode
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Filter & Pagination Info -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Tampilkan</span>
                            <select class="rounded border-gray-300 text-sm py-1" onchange="window.location.href='?per_page='+this.value">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">entri</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Cari:</span>
                            <input type="text" id="searchInput" placeholder="" class="rounded border-gray-300 text-sm py-1 px-2 w-48">
                        </div>
                    </div>

                    <!-- Tabel Kode Aktivasi (Grouped by Batch) -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r">
                                        Nama
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r">
                                        QTY
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r">
                                        Modul
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r">
                                        Start Test At
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    // Group codes by batch_id or test_id + created_at
                                    $groupedCodes = $codes->groupBy(function($code) {
                                        return $code->batch_id ?? ($code->test_id . '_' . $code->created_at->format('YmdHis'));
                                    });
                                @endphp

                                @forelse ($groupedCodes as $batchKey => $batchCodes)
                                    @php
                                        $firstCode = $batchCodes->first();
                                        $totalQty = $batchCodes->count();
                                        $usedCount = $batchCodes->where('status', 'Used')->count();
                                        $batchStatus = $usedCount == $totalQty ? 'Completed' : ($usedCount > 0 ? 'On Progress' : 'Pending');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm border-r">
                                            <a href="{{ route('admin.codes.show', $firstCode->id) }}?batch={{ $batchKey }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                {{ $firstCode->batch_name ?? $firstCode->test->title ?? 'Batch ' . substr($batchKey, 0, 8) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ $totalQty }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ $firstCode->test->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ $firstCode->start_test_at ? \Carbon\Carbon::parse($firstCode->start_test_at)->format('Y-m-d H:i:s') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm border-r">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                                @if($batchStatus == 'On Progress') bg-blue-100 text-blue-800
                                                @elseif($batchStatus == 'Completed') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ $batchStatus }}
                                            </span>
                                            <span class="text-xs text-gray-500 ml-2">({{ $usedCount }}/{{ $totalQty }})</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.codes.show', $firstCode->id) }}?batch={{ $batchKey }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                    Detail
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <button type="button" onclick="deleteBatch('{{ $batchKey }}', {{ $batchCodes->pluck('id')->toJson() }})" class="text-red-600 hover:text-red-800 font-medium">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                            Belum ada kode aktivasi. Klik tombol "Add" untuk membuat kode baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Info -->
                    <div class="flex justify-between items-center mt-4">
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $codes->firstItem() ?? 0 }} sampai {{ $codes->lastItem() ?? 0 }} dari {{ $codes->total() }} entri
                        </div>
                        <div>
                            {{ $codes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for batch delete -->
    <form id="batchDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="batch_ids" id="batchIdsInput">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFormBtn = document.getElementById('toggleFormBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const formContainer = document.getElementById('formContainer');

            toggleFormBtn.addEventListener('click', function() {
                if (formContainer.style.display === 'none') {
                    formContainer.style.display = 'block';
                    toggleFormBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Close';
                } else {
                    formContainer.style.display = 'none';
                    toggleFormBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Add';
                }
            });

            cancelBtn.addEventListener('click', function() {
                formContainer.style.display = 'none';
                toggleFormBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Add';
            });

            @if($errors->any())
                formContainer.style.display = 'block';
                toggleFormBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Close';
            @endif
        });

        function deleteBatch(batchKey, batchIds) {
            if (confirm('Yakin ingin menghapus batch ini beserta ' + batchIds.length + ' kode aktivasi?')) {
                const form = document.getElementById('batchDeleteForm');
                document.getElementById('batchIdsInput').value = JSON.stringify(batchIds);
                form.action = '{{ route("admin.codes.destroy", ":id") }}'.replace(':id', batchIds[0]);
                form.submit();
            }
        }
    </script>
</x-admin-layout>