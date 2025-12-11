<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('2. Atur Urutan Pengerjaan Alat Tes') }}
        </h2>
        {{-- CDN SortableJS untuk fungsionalitas drag and drop --}}
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                
                {{-- DIUBAH: action mengarah ke rute final store --}}
                <form method="POST" action="{{ route('admin.tests.store.final') }}">
                    @csrf

                    {{-- HIDDEN INPUT: Data Alat Tes yang diurutkan (JSON array of IDs) --}}
                    <input type="hidden" name="test_order" id="test_order_input" value="[]">

                    <div class="border border-indigo-300 bg-indigo-50 p-4 rounded-lg mb-6">
                        <p class="font-semibold text-indigo-700 mb-2">Instruksi Urutan:</p>
                        <p class="text-sm text-indigo-600">
                            Tarik dan lepaskan setiap Alat Tes di bawah ini untuk **mengatur urutan pengerjaan** yang akan dilihat oleh peserta.
                            Urutan default yang disarankan adalah urutan yang ada saat ini.
                        </p>
                    </div>

                    <div class="space-y-3 p-4 border rounded-lg bg-gray-50" id="alat_tes_list_container">
                        {{-- Daftar Alat Tes akan ditampilkan di sini --}}
                        @foreach ($alatTesList as $alatTes)
                            <div class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm cursor-move sortable-item"
                                data-id="{{ $alatTes->id }}">
                                <svg class="w-5 h-5 mr-3 text-gray-400 handle" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ $loop->iteration }}. {{ $alatTes->name }} ({{ $alatTes->duration_minutes }} menit)</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <p class="mt-4 text-xs text-red-600">
                        Pastikan Anda menekan tombol "Selesaikan & Simpan Modul" untuk menyimpan perubahan urutan ini.
                    </p>

                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('admin.tests.create') }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            &laquo; Kembali ke Data Dasar
                        </a>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                            Selesaikan & Simpan Modul
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderInput = document.getElementById('test_order_input');
            const listContainer = document.getElementById('alat_tes_list_container');

            function updateOrder() {
                // Ambil data-id dari semua item sesuai urutan di DOM
                const orderedIds = Array.from(listContainer.querySelectorAll('.sortable-item'))
                    .map(item => item.getAttribute('data-id'));
                
                // Perbarui nilai hidden input 'test_order'
                orderInput.value = JSON.stringify(orderedIds);
            }

            // Inisialisasi SortableJS
            new Sortable(listContainer, {
                animation: 150,
                handle: '.handle', 
                onEnd: function(evt) {
                    updateOrder(); 
                },
            });

            // Jalankan sekali saat halaman dimuat untuk inisialisasi hidden input
            updateOrder();
        });
    </script>
</x-admin-layout>