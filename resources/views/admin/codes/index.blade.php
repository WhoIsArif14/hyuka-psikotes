<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kode Aktivasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ALERTS --}}
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

            {{-- CARD UTAMA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- HEADER --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Kode Aktivasi</h3>
                        <button type="button" id="toggleFormBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add
                        </button>
                    </div>

                    {{-- FORM GENERATE (Modal) --}}
                    <!-- Modal backdrop -->
                    <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 hidden z-40"></div>

                    <!-- Modal -->
                    <div id="createBatchModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4">
                            <div class="flex items-center justify-between p-5 border-b">
                                <h4 class="text-lg font-semibold text-gray-900">Buat Kode Aktivasi Massal</h4>
                                <button id="modalCloseBtn" class="text-gray-500 hover:text-gray-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('admin.codes.store') }}" id="createBatchForm">
                                @csrf
                                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-1">
                                        <label for="modal_batch_name"
                                            class="block text-sm font-medium text-gray-700 mb-1">Nama Batch
                                            (Opsional)</label>
                                        <input type="text" name="batch_name" id="modal_batch_name"
                                            placeholder="Contoh: Batch Januari 2025"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2" />
                                        <p class="mt-2 text-xs text-gray-500">Masukkan nama batch yang mudah diingat.
                                            Jika dikosongkan, sistem akan membuat nama otomatis.</p>
                                    </div>

                                    <div>
                                        <label for="modal_test_id"
                                            class="block text-sm font-medium text-gray-700 mb-1">Pilih Modul Tes</label>
                                        <select name="test_id" id="modal_test_id" required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                                            <option value="">-- Pilih Modul --</option>
                                            @foreach ($tests as $test)
                                                <option value="{{ $test->id }}">{{ $test->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="modal_quantity"
                                            class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kode
                                            (Qty)</label>
                                        <input type="number" name="quantity" id="modal_quantity" required
                                            min="1" max="1000" value="10"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2" />
                                        <p class="mt-2 text-xs text-gray-500">Kami sarankan membuat batch 10-100 untuk
                                            kemudahan distribusi.</p>
                                    </div>

                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Preview</label>
                                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                            <p class="text-sm text-gray-700 mb-1"><strong>Nama Batch:</strong> <span
                                                    id="previewBatchName" class="text-gray-800">-</span></p>
                                            <p class="text-sm text-gray-700 mb-1"><strong>Qty:</strong> <span
                                                    id="previewQty" class="text-gray-800">10</span></p>
                                            <p class="text-sm text-gray-500">Setelah generate, semua kode akan muncul di
                                                detail batch.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 p-4 border-t">
                                    <button type="button" id="modalCancelBtn"
                                        class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">Generate
                                        Kode</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($errors->any())
                        <script>
                            window.addEventListener('DOMContentLoaded', function() {
                                openCreateBatchModal();
                            });
                        </script>
                    @endif

                    {{-- FILTER --}}
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Tampilkan</span>
                            <select class="rounded border-gray-300 text-sm py-1"
                                onchange="window.location.href='?per_page='+this.value">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">entri</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Cari:</span>
                            <input type="text" id="searchInput" placeholder=""
                                class="rounded border-gray-300 text-sm py-1 px-2 w-48">
                        </div>
                    </div>

                    {{-- TABEL --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                        Nama</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                        QTY</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                        Modul</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                        Start Test At</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">
                                        Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($codes as $batch)
                                    @php
                                        $totalQty = $batch->total_qty ?? 0;
                                        $usedCount = $batch->used_count ?? 0;
                                        $batchStatus =
                                            $usedCount == $totalQty
                                                ? 'Completed'
                                                : ($usedCount > 0
                                                    ? 'On Progress'
                                                    : 'Pending');
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm border-r">
                                            <a href="{{ route('admin.codes.show', $batch->id) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                {{ $batch->batch_name ?? ($batch->test->title ?? 'Batch ' . $loop->iteration) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ $totalQty }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ $batch->test->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 border-r">
                                            {{ isset($batch->start_test_at) && $batch->start_test_at
                                                ? \Carbon\Carbon::parse($batch->start_test_at)->format('Y-m-d H:i:s')
                                                : '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm border-r">
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-full
                                                @if ($batchStatus == 'On Progress') bg-blue-100 text-blue-800
                                                @elseif($batchStatus == 'Completed') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $batchStatus }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-500 ml-2">({{ $usedCount }}/{{ $totalQty }})</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.codes.show', $batch->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                                    Detail
                                                </a>
                                                <span class="text-gray-300">|</span>

                                                {{-- HAPUS --}}
                                                <form action="{{ route('admin.codes.destroy', $batch->id) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Yakin ingin menghapus batch ini ({{ $totalQty }} kode)?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium">
                                                        Hapus
                                                    </button>
                                                </form>
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

                    {{-- PAGINATION --}}
                    <div class="flex justify-between items-center mt-4">
                        <div class="text-sm text-gray-600">
                            Menampilkan {{ $codes->firstItem() ?? 0 }} sampai {{ $codes->lastItem() ?? 0 }} dari
                            {{ $codes->total() }} entri
                        </div>
                        <div>
                            {{ $codes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        // Modal control functions (open/close) and live preview
        function openCreateBatchModal() {
            document.getElementById('createBatchModal').classList.remove('hidden');
            document.getElementById('modalBackdrop').classList.remove('hidden');
            // focus first input
            const first = document.getElementById('modal_batch_name');
            if (first) first.focus();
        }

        function closeCreateBatchModal() {
            document.getElementById('createBatchModal').classList.add('hidden');
            document.getElementById('modalBackdrop').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('toggleFormBtn');
            const closeBtn = document.getElementById('modalCloseBtn');
            const cancelBtn = document.getElementById('modalCancelBtn');
            const backdrop = document.getElementById('modalBackdrop');
            const batchNameInput = document.getElementById('modal_batch_name');
            const qtyInput = document.getElementById('modal_quantity');
            const previewName = document.getElementById('previewBatchName');
            const previewQty = document.getElementById('previewQty');

            if (openBtn) {
                openBtn.addEventListener('click', function() {
                    openCreateBatchModal();
                });
            }

            if (closeBtn) closeBtn.addEventListener('click', closeCreateBatchModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeCreateBatchModal);
            if (backdrop) backdrop.addEventListener('click', closeCreateBatchModal);

            // close on Esc
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCreateBatchModal();
                }
            });

            // live preview
            function updatePreview() {
                const name = batchNameInput.value.trim();
                previewName.textContent = name ? name : 'Auto-generated name';
                previewQty.textContent = qtyInput.value;
            }

            if (batchNameInput) batchNameInput.addEventListener('input', updatePreview);
            if (qtyInput) qtyInput.addEventListener('input', updatePreview);

            // initialize preview
            updatePreview();

            @if ($errors->any())
                // If server validation failed, open modal and keep input values
                openCreateBatchModal();
            @endif
        });
    </script>
</x-admin-layout>
