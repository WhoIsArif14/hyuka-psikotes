<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Hasil Tes Peserta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-900">Daftar Kode Terpakai & Hasil Tes</h3>
                    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Cari kode aktivasi..."
                            class="border-gray-300 rounded-md shadow-sm pl-3 pr-2 py-1 text-sm" />
                        <select name="test_id" class="border-gray-300 rounded-md shadow-sm pl-2 pr-2 py-1 text-sm">
                            <option value="">Semua Modul</option>
                            @foreach ($tests as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('test_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">Cari</button>
                        @if (request('q') || request('test_id'))
                            <a href="{{ route('admin.reports.index') }}"
                                class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm">Reset</a>
                        @endif
                    </form>
                </div>

                {{-- Notifikasi --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.reports.bulk_destroy') }}">
                        @csrf
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="h-4 w-4">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kode Aktivasi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peserta
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Tes
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Hasil
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Laporan PDF / Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($usedCodes as $code)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <input type="checkbox" name="selected[]" value="{{ $code->id }}"
                                                class="select-row h-4 w-4">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $code->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($code->user)
                                                <a href="{{ route('admin.reports.participant', $code->user->id) }}" class="text-blue-600 hover:underline font-medium">
                                                    {{ $code->user->name }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $code->test->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $code->user && $code->user->latestTestResult ? 'Selesai' : 'Belum Ada Hasil' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                @if ($code->user)
                                                    {{-- Tautan ke detail laporan peserta --}}
                                                    <a href="{{ route('admin.reports.participant', $code->user->id) }}"
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 px-3 py-1 rounded-md">
                                                        Detail
                                                    </a>
                                                @endif
                                                @if ($code->user && $code->user->latestTestResult)
                                                    {{-- Tautan ke PDF menggunakan ID Hasil Tes --}}
                                                    <a href="{{ route('admin.reports.pdf', $code->user->latestTestResult->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 px-3 py-1 rounded-md">
                                                        PDF
                                                    </a>
                                                @endif

                                                {{-- Delete activation code (handled via JS to avoid nested forms) --}}
                                                <button type="button"
                                                    onclick="submitDelete('{{ route('admin.reports.destroy', $code) }}')"
                                                    class="px-3 py-1 bg-red-500 text-white rounded-md text-sm">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada kode aktivasi yang sudah terpakai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-md"
                                    onclick="return confirm('Yakin ingin menghapus kode-kode yang dipilih?')">Hapus
                                    Terpilih</button>
                            </div>
                            <div>
                                {{ $usedCodes->links() }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select all toggle
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const rows = document.querySelectorAll('.select-row');
            const bulkDeleteButton = document.querySelector('#bulk-action-form button[type="submit"]');

            function updateBulkButtonState() {
                const anyChecked = Array.from(rows).some(r => r.checked);
                if (bulkDeleteButton) bulkDeleteButton.disabled = !anyChecked;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    rows.forEach(r => r.checked = selectAll.checked);
                    updateBulkButtonState();
                });
            }

            rows.forEach(r => r.addEventListener('change', updateBulkButtonState));

            // Initialize state
            updateBulkButtonState();
        });

        // Helper to submit DELETE requests with CSRF via dynamically created form
        function submitDelete(url) {
            if (!confirm('Yakin ingin menghapus kode aktivasi ini? Aksi tidak dapat dibatalkan.')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-admin-layout>
