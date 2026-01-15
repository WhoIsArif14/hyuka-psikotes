<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Per Alat Tes - {{ $alatTes->name }}
            </h2>
            <a href="{{ route('admin.reports.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informasi Alat Tes --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Informasi Alat Tes
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Nama Alat Tes</p>
                        <p class="font-semibold text-gray-800">{{ $alatTes->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Slug</p>
                        <p class="font-semibold text-gray-800 font-mono">{{ $alatTes->slug ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Durasi</p>
                        <p class="font-semibold text-gray-800">{{ $alatTes->duration ?? '-' }} menit</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Jumlah Soal</p>
                        <p class="font-semibold text-gray-800">{{ $alatTes->questions_count ?? $alatTes->questions()->count() ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistik Hasil Tes
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-xs text-blue-600 font-medium">Total Peserta</p>
                        <p class="text-3xl font-bold text-blue-800">{{ $stats['total_participants'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-xs text-green-600 font-medium">Selesai</p>
                        <p class="text-3xl font-bold text-green-800">{{ $stats['completed_count'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <p class="text-xs text-purple-600 font-medium">Rata-rata Skor</p>
                        <p class="text-3xl font-bold text-purple-800">{{ number_format($stats['avg_score'] ?? 0, 1) }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <p class="text-xs text-yellow-600 font-medium">Skor Tertinggi</p>
                        <p class="text-3xl font-bold text-yellow-800">{{ $stats['max_score'] ?? 0 }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <p class="text-xs text-red-600 font-medium">Skor Terendah</p>
                        <p class="text-3xl font-bold text-red-800">{{ $stats['min_score'] ?? 0 }}</p>
                    </div>
                </div>

                {{-- Export Button --}}
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.reports.export', ['alat_tes_id' => $alatTes->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export CSV
                    </a>
                </div>
            </div>

            {{-- PAPI Results (if this is PAPI tool) --}}
            @if($isPapi && $papiResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Hasil PAPI Kostick
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">G</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">L</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">I</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">T</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">V</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">S</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">R</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">D</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">C</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">E</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($papiResults as $papi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.reports.participant', $papi->user_id) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $papi->user->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $papi->test->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->g ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->l ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->i ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->t ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->v ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->s ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->r ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->d ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->c ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-center">{{ $papi->e ?? 0 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $papi->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <a href="{{ route('admin.reports.participant', $papi->user_id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $papiResults->links() }}
                </div>
            </div>
            @endif

            {{-- RMIB Results (if this is RMIB tool) --}}
            @if($isRmib && $rmibResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                    </svg>
                    Hasil RMIB (Rothwell-Miller Interest Blank)
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Top 3 Minat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rmibResults as $rmib)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.reports.participant', $rmib->user_id) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $rmib->user->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $rmib->test->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($rmib->top_interests)
                                        @php
                                            $topInterests = is_string($rmib->top_interests) ? json_decode($rmib->top_interests, true) : $rmib->top_interests;
                                        @endphp
                                        @if(is_array($topInterests))
                                            <div class="flex gap-1 flex-wrap">
                                                @foreach(array_slice($topInterests, 0, 3) as $idx => $interest)
                                                <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">
                                                    #{{ $idx + 1 }} {{ ucfirst($interest) }}
                                                </span>
                                                @endforeach
                                            </div>
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $rmib->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <a href="{{ route('admin.reports.participant', $rmib->user_id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rmibResults->links() }}
                </div>
            </div>
            @endif

            {{-- General Test Results --}}
            @if($testResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Daftar Hasil Tes Peserta
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Skor</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">IQ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Selesai</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($testResults as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.reports.participant', $result->user_id) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $result->participant_name ?? $result->user->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $result->email ?? $result->user->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $result->test->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded font-semibold">{{ $result->score ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    @if($result->iq)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded font-semibold">{{ $result->iq }}</span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $result->status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $result->status ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $result->completed_at ?? $result->created_at }}</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.reports.participant', $result->user_id) }}"
                                            class="text-indigo-600 hover:text-indigo-800 font-medium">Detail</a>
                                        <a href="{{ route('admin.reports.pdf', $result->id) }}"
                                            class="text-green-600 hover:text-green-800 font-medium">PDF</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $testResults->links() }}
                </div>
            </div>
            @endif

            {{-- No Results --}}
            @if($testResults->count() == 0 && (!$isPapi || $papiResults->count() == 0) && (!$isRmib || $rmibResults->count() == 0))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada hasil tes</h3>
                    <p class="text-gray-500">Belum ada peserta yang menyelesaikan alat tes ini.</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>
