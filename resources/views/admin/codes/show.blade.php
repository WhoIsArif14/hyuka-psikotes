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
                    <a href="{{ route('admin.codes.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        ← Kembali ke Daftar
                    </a>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Informasi Batch</h3>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <span class="text-sm text-gray-600">Nama Modul:</span>
                            <p class="font-medium">{{ $code->test->title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Jumlah Kode:</span>
                            <p class="font-medium">{{ $batchCodes->count() }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Tanggal Generate:</span>
                            <p class="font-medium">{{ $code->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Kode Digunakan:</span>
                            <p class="font-medium">{{ $batchCodes->where('status', 'Used')->count() }} / {{ $batchCodes->count() }}</p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold mb-4">Daftar Kode Aktivasi</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Kode Aktivasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Digunakan Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal Digunakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($batchCodes as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm border-r">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-mono font-semibold border-r">{{ $item->code }}</td>
                                <td class="px-4 py-3 text-sm border-r">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($item->status == 'Used' || $item->status == 'Completed') bg-green-100 text-green-800
                                        @elseif($item->status == 'Active' || $item->status == 'On Progress') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $item->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm border-r">
                                    {{ $item->user_name ?? ($item->user->name ?? '-') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d-m-Y H:i') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
                        🖨️ Print / Export
                    </button>
                    <a href="{{ route('admin.codes.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>