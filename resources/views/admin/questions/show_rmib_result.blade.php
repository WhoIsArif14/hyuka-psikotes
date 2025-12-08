<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hasil Tes RMIB - {{ $result->user->name }}
            </h2>
            <a href="{{ route('admin.rmib-results.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                ← Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Success -->
            <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white rounded-lg shadow-lg p-8 mb-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-2">📊 Hasil Tes RMIB</h1>
                    <p class="text-xl opacity-90">{{ $result->user->name }}</p>
                    <p class="text-sm opacity-75 mt-1">{{ $result->user->email }}</p>
                </div>
            </div>

            <!-- Top 3 Interests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🏆 Top 3 Minat</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($result->getTopInterests() as $interest)
                        <div class="bg-gradient-to-br from-{{ $interest['rank'] == 1 ? 'yellow' : ($interest['rank'] == 2 ? 'gray' : 'orange') }}-100 to-{{ $interest['rank'] == 1 ? 'yellow' : ($interest['rank'] == 2 ? 'gray' : 'orange') }}-200 rounded-lg p-6 text-center border-2 border-{{ $interest['rank'] == 1 ? 'yellow' : ($interest['rank'] == 2 ? 'gray' : 'orange') }}-400">
                            <div class="text-4xl mb-3">
                                @if($interest['rank'] == 1)
                                    🥇
                                @elseif($interest['rank'] == 2)
                                    🥈
                                @else
                                    🥉
                                @endif
                            </div>
                            <h3 class="font-bold text-lg mb-2">{{ $interest['name'] }}</h3>
                            <div class="text-3xl font-bold text-gray-800">{{ $interest['score'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">poin</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- All Scores Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">📊 Skor Lengkap Semua Kategori Minat</h2>
                    
                    <div class="space-y-4">
                        @php
                            $scores = $result->getScoresArray();
                            arsort($scores);
                            $maxScore = max(array_column($scores, 'score'));
                        @endphp

                        @foreach($scores as $code => $data)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-gray-700">{{ $data['name'] }}</span>
                                <span class="text-lg font-bold text-blue-600">{{ $data['score'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" 
                                     style="width: {{ $maxScore > 0 ? ($data['score'] / $maxScore * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Interpretation -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
                <h3 class="font-bold text-xl text-blue-800 mb-4">💡 Interpretasi Hasil</h3>
                <div class="text-blue-700 space-y-3">
                    <p><strong>Minat tertinggi:</strong> {{ $result->getTopInterests()[0]['name'] ?? 'N/A' }}</p>
                    <p>Peserta menunjukkan minat yang kuat dalam bidang ini. Cocok untuk karir atau aktivitas yang sesuai dengan minat tersebut.</p>
                    
                    <div class="mt-4">
                        <p class="font-semibold mb-2">Rekomendasi:</p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Eksplorasi lebih dalam tentang bidang minat tertinggi</li>
                            <li>Cari peluang pengembangan di area ini</li>
                            <li>Konsultasi lanjutan untuk guidance karir</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 bg-gray-50">
                    <h3 class="font-semibold text-gray-700 mb-4">📋 Detail Tes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nama Peserta:</span>
                            <span class="font-semibold ml-2">{{ $result->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold ml-2">{{ $result->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Alat Tes:</span>
                            <span class="font-semibold ml-2">{{ $result->alatTes->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tanggal Selesai:</span>
                            <span class="font-semibold ml-2">{{ $result->completed_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center no-print">
                <a href="{{ route('admin.rmib-results.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Kembali
                </a>
                
                <div class="space-x-3">
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        🖨️ Cetak
                    </button>
                    
                    <form method="POST" 
                          action="{{ route('admin.rmib-results.destroy', $result) }}" 
                          class="inline"
                          onsubmit="return confirm('Yakin ingin menghapus hasil ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            font-size: 12pt;
        }
        
        .bg-gradient-to-r,
        .bg-gradient-to-br {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
    </style>
    @endpush
</x-admin-layout>