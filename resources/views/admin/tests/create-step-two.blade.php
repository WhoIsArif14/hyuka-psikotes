<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('2. Atur Urutan Pengerjaan Alat Tes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="text-sm font-medium text-gray-700 mb-4">
                    <p class="font-bold">Modul: {{ $tempData['title'] ?? 'Nama Modul' }}</p>
                    <p class="text-gray-500">Total Alat Tes: {{ count($alatTesList) }} | Total Durasi: {{ $tempData['duration_minutes'] ?? 0 }} menit</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"> Terjadi kesalahan saat menyimpan.</span>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.tests.store.final') }}">
                    @csrf

                    {{-- HIDDEN INPUT: Data Alat Tes yang diurutkan (JSON array) --}}
                    <input type="hidden" name="test_order" id="test_order_input" value="[]">

                    <div class="border border-blue-300 bg-blue-50 p-4 rounded-lg mb-6">
                        <p class="font-semibold text-blue-700 mb-2">Atur urutan tes</p>
                        <p class="text-sm text-gray-600">
                            <strong>NOTE:</strong> Atur Urutan tes lewat mana urutan atau mengubah urutan tes
                        </p>
                    </div>

                    {{-- Tabel Atur Urutan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">
                                        Nama Tes
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b">
                                        Urutan
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b">
                                        Ubah Urutan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($alatTesList as $index => $alatTes)
                                <tr class="hover:bg-gray-50" data-id="{{ $alatTes->id }}" data-original-index="{{ $index }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $alatTes->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">
                                        <span class="order-display">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <select class="order-select border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                data-test-id="{{ $alatTes->id }}">
                                            @for ($i = 1; $i <= count($alatTesList); $i++)
                                                <option value="{{ $i }}" {{ $i == ($index + 1) ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <p class="mt-4 text-xs text-red-600">
                        Pastikan Anda menekan tombol "Lanjutkan" untuk menyimpan perubahan urutan ini.
                    </p>

                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('admin.tests.create') }}"
                            class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Lanjutkan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderSelects = document.querySelectorAll('.order-select');
            const orderInput = document.getElementById('test_order_input');
            const tableBody = document.querySelector('tbody');

            function updateOrder() {
                // Ambil semua baris sesuai urutan saat ini di DOM
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                const orderedIds = rows.map(row => row.getAttribute('data-id'));
                
                // Update hidden input
                orderInput.value = JSON.stringify(orderedIds);
                
                // Update tampilan nomor urutan
                rows.forEach((row, idx) => {
                    row.querySelector('.order-display').textContent = idx + 1;
                });
            }

            // Handler untuk perubahan dropdown
            orderSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const newPosition = parseInt(this.value) - 1;
                    const currentRow = this.closest('tr');
                    const rows = Array.from(tableBody.querySelectorAll('tr'));
                    const currentIndex = rows.indexOf(currentRow);

                    // Pindahkan baris ke posisi baru
                    if (newPosition !== currentIndex) {
                        tableBody.removeChild(currentRow);
                        
                        if (newPosition >= rows.length - 1) {
                            tableBody.appendChild(currentRow);
                        } else {
                            tableBody.insertBefore(currentRow, rows[newPosition]);
                        }

                        // Update semua dropdown berdasarkan urutan baru
                        const updatedRows = Array.from(tableBody.querySelectorAll('tr'));
                        updatedRows.forEach((row, idx) => {
                            row.querySelector('.order-select').value = idx + 1;
                        });

                        updateOrder();
                    }
                });
            });

            // Inisialisasi urutan awal
            updateOrder();
        });
    </script>
</x-admin-layout>