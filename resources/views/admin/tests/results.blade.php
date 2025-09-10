<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    {{-- Inisialisasi Alpine.js untuk modal --}}
    <div x-data="{ open: false, selectedResult: {} }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.tests.export', $test) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Ekspor ke Excel
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendidikan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Tes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor</th>
                                    <th class="relative px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($results as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">{{ $result->participant_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $result->education }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-lg text-blue-600">{{ $result->score }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- Tombol untuk membuka modal --}}
                                            <button @click="open = true; selectedResult = {{ json_encode($result) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada peserta yang menyelesaikan tes ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 p-6 border-t">
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk Detail Peserta -->
        <div x-show="open" @click.away="open = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50" style="display: none;">
            <div @click.stop class="bg-white rounded-lg shadow-xl overflow-hidden max-w-lg w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900" x-text="`Detail Peserta: ${selectedResult.participant_name}`"></h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <p><strong>Email:</strong> <span x-text="selectedResult.participant_email"></span></p>
                        <p><strong>No. HP:</strong> <span x-text="selectedResult.phone_number"></span></p>
                        <p><strong>Pendidikan:</strong> <span x-text="selectedResult.education"></span></p>
                        <p><strong>Jurusan:</strong> <span x-text="selectedResult.major"></span></p>
                        <hr class="my-2">
                        <p><strong>Skor Tes:</strong> <span class="font-bold text-lg text-blue-600" x-text="selectedResult.score"></span></p>
                        <p><strong>Waktu Mengerjakan:</strong> <span x-text="new Date(selectedResult.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })"></span></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 text-right">
                    <button @click="open = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

