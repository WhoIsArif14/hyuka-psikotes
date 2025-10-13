<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modul
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header Halaman -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Modul</h3>
                            <p class="text-gray-500 text-sm mt-1">Daftar semua modul tes yang tersedia di dalam sistem.</p>
                        </div>
                        <a href="{{ route('admin.tests.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Add Modul
                        </a>
                    </div>

                    <!-- Kontrol Tabel (Search & Entries) -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center">
                            <select class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select>
                            <span class="ml-2 text-gray-700 text-sm">entri</span>
                        </div>
                        <div class="relative">
                            <input type="text" placeholder="Cari modul..." class="border-gray-300 rounded-md shadow-sm pl-10 text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Modul -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($tests as $test)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $test->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($test->is_published)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Published
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.tests.results', $test) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs">Detail</a>
                                                <a href="{{ route('admin.tests.edit', $test) }}" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 text-xs">Edit</a>
                                                
                                                {{-- Aksi Delete --}}
                                                {{-- Hapus onsubmit="return confirm(...)" --}}
                                                <form id="delete-form-{{ $test->id }}" action="{{ route('admin.tests.destroy', $test) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    {{-- Tambahkan data modul untuk JS --}}
                                                    <button type="button" 
                                                        class="delete-button px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 text-xs"
                                                        data-module-id="{{ $test->id }}"
                                                        data-module-name="{{ $test->title }}">
                                                        Hapus
                                                    </button>
                                                </form>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Belum ada modul yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    {{-- ========================================================================= --}}
    {{-- MODAL KONFIRMASI HAPUS BARU --}}
    {{-- ========================================================================= --}}
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Konfirmasi Penghapusan Modul
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda yakin ingin menghapus modul <span id="moduleName" class="font-semibold text-red-600"></span>?
                                </p>
                                <p class="text-sm text-red-700 font-medium mt-1">
                                    ⚠️ Perhatian: Semua data terkait (seperti soal, jawaban, dan hasil tes yang sudah ada) akan ikut terhapus secara permanen. Aksi ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteButton"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Hapus Modul Ini
                    </button>
                    <button type="button" id="cancelDeleteButton"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- SCRIPT JAVASCRIPT --}}
    {{-- ========================================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('deleteModal');
            const confirmButton = document.getElementById('confirmDeleteButton');
            const cancelButton = document.getElementById('cancelDeleteButton');
            const moduleNameSpan = document.getElementById('moduleName');
            let formToSubmit = null; // Menyimpan referensi form yang akan di-submit

            // 1. Tambahkan Event Listener ke semua tombol Hapus
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const moduleId = this.dataset.moduleId;
                    const moduleName = this.dataset.moduleName;
                    
                    // Set referensi form yang benar
                    formToSubmit = document.getElementById(`delete-form-${moduleId}`);
                    
                    // Isi nama modul di modal
                    moduleNameSpan.textContent = `"${moduleName}"`;
                    
                    // Tampilkan modal
                    modal.classList.remove('hidden');
                });
            });

            // 2. Aksi tombol Konfirmasi Hapus
            confirmButton.addEventListener('click', function() {
                if (formToSubmit) {
                    formToSubmit.submit(); // Kirim form DELETE
                }
                modal.classList.add('hidden');
            });

            // 3. Aksi tombol Batal
            cancelButton.addEventListener('click', function() {
                modal.classList.add('hidden');
                formToSubmit = null; // Reset form
            });

            // Opsional: Tutup modal ketika mengklik area gelap (overlay)
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    formToSubmit = null;
                }
            });
        });
    </script>
</x-admin-layout>