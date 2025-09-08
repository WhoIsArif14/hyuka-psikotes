<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Kode Aktivasi: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form Generate Kode Baru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Generate Kode Aktivasi Baru</h3>
                    <p class="text-sm text-gray-600 mb-4">Masukkan jumlah peserta untuk membuat kode aktivasi massal. Semua kode akan berlaku selama 24 jam.</p>
                    
                    {{-- PERBAIKAN ADA DI BARIS ACTION DI BAWAH INI --}}
                    <form method="POST" action="{{ route('admin.codes.store', $test) }}">
                        @csrf
                        <div class="flex items-end gap-4">
                           <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Kode</label>
                                <input type="number" name="quantity" id="quantity" required min="1" max="500" value="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                           </div>
                           <div>
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Buat Kode
                                </button>
                           </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Menampilkan Kode yang Baru Dibuat -->
            @if (session('generated_codes'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Kode Baru (Salin & Bagikan):</h3>
                         <textarea readonly class="w-full h-40 font-mono text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">{{ implode("\n", session('generated_codes')) }}</textarea>
                    </div>
                </div>
            @endif


            <!-- Daftar Kode Aktivasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Kode & Status</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Aktivasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Digunakan Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlaku Hingga</th>
                                    <th class="relative px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($codes as $code)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-gray-700">{{ $code->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($code->completed_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                            @elseif($code->user_id)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Digunakan</span>
                                            @elseif($code->expires_at->isPast())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Kadaluarsa</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tersedia</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $code->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $code->expires_at->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <form method="POST" action="{{ route('admin.codes.destroy', $code) }}" onsubmit="return confirm('Yakin ingin menghapus kode ini? Kode akan menjadi tidak valid.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada kode aktivasi yang dibuat untuk tes ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

