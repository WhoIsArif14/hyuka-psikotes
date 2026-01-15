<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Peserta -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Peserta</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalPeserta) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Klien -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Klien</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalKlien) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Modul Tes -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Modul Tes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalModul) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Alat Tes -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Alat Tes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalAlatTes) }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Aktivitas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Peserta Hari Ini -->
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-100">Peserta Hari Ini</p>
                        <p class="text-4xl font-bold mt-1">{{ number_format($pesertaHariIni) }}</p>
                        <p class="text-sm text-indigo-200 mt-2">Mengerjakan tes hari ini</p>
                    </div>
                    <div class="p-4 bg-white/20 rounded-full">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tes Sedang Berlangsung -->
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-100">Tes Berlangsung</p>
                        <p class="text-4xl font-bold mt-1">{{ number_format($tesSedangBerlangsung) }}</p>
                        <p class="text-sm text-emerald-200 mt-2">Sedang dikerjakan</p>
                    </div>
                    <div class="p-4 bg-white/20 rounded-full">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kode Aktivasi -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-100">Kode Aktivasi</p>
                        <p class="text-4xl font-bold mt-1">{{ number_format($kodeAktivasiTerpakai) }}<span class="text-xl font-normal">/{{ number_format($totalKodeAktivasi) }}</span></p>
                        <p class="text-sm text-amber-200 mt-2">{{ number_format($kodeAktivasi) }} tersedia</p>
                    </div>
                    <div class="p-4 bg-white/20 rounded-full">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik dan Aktivitas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Grafik Tren Peserta -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Peserta (7 Hari Terakhir)</h3>
                <div class="h-64">
                    <canvas id="pesertaChart"></canvas>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @forelse ($aktivitasTerbaru as $aktivitas)
                        <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-sm">
                                        {{ strtoupper(substr($aktivitas->participant_name ?? ($aktivitas->user->name ?? 'P'), 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $aktivitas->participant_name ?? ($aktivitas->user->name ?? 'Peserta') }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $aktivitas->test->title ?? 'Tes' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">
                                    {{ $aktivitas->created_at->diffForHumans() }}
                                </p>
                                @if($aktivitas->end_time)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Berlangsung
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2">Belum ada aktivitas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tes Populer -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Tes Terpopuler</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tes</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pengerjaan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rangkumanTes as $tes)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $tes->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($tes->test_results_count) }}</span>
                                        <span class="ml-1 text-sm text-gray-500">peserta</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($tes->is_published)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.tests.show', $tes) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Belum ada tes yang dikerjakan oleh peserta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById('pesertaChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Peserta',
                    data: @json($chartData),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>
