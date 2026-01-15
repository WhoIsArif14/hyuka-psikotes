<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Hasil Tes - {{ $user->name }}
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

            {{-- Informasi Peserta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Peserta
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Nama Lengkap</p>
                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="font-semibold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Nomor HP</p>
                        <p class="font-semibold text-gray-800">{{ $user->phone_number ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Pendidikan</p>
                        <p class="font-semibold text-gray-800">{{ $user->education ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Jurusan</p>
                        <p class="font-semibold text-gray-800">{{ $user->major ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500">Tanggal Daftar</p>
                        <p class="font-semibold text-gray-800">{{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Modul yang Diikuti --}}
            @if($activationCodes->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Modul Tes yang Diikuti
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($activationCodes as $code)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $code->test->name ?? 'Modul Tidak Diketahui' }}</p>
                                <p class="text-xs text-gray-500 mt-1">Kode: <span class="font-mono">{{ $code->code }}</span></p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $code->status === 'Used' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $code->status }}
                            </span>
                        </div>
                        @if($code->used_at)
                        <p class="text-xs text-gray-400 mt-2">Digunakan: {{ $code->used_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Hasil Tes Umum --}}
            @if($testResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Hasil Tes
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alat Tes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IQ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Selesai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($testResults as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $result->test->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $result->alatTes->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded font-semibold">{{ $result->score ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
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
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.reports.pdf', $result->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Download PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Hasil PAPI Kostick --}}
            @if($papiResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Hasil PAPI Kostick
                </h3>

                @foreach($papiResults as $papi)
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $papi->test->name ?? 'Modul PAPI' }}</p>
                            <p class="text-xs text-gray-500">{{ $papi->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($papi->profile_type)
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                            {{ $papi->profile_type }}
                        </span>
                        @endif
                    </div>

                    {{-- PAPI Dimension Scores --}}
                    <div class="grid grid-cols-4 md:grid-cols-5 lg:grid-cols-10 gap-2 mt-4">
                        @php
                            $dimensions = ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'];
                        @endphp
                        @foreach($dimensions as $dim)
                        @php
                            $score = $papi->{strtolower($dim)} ?? 0;
                            $bgColor = $score >= 7 ? 'bg-green-100' : ($score >= 4 ? 'bg-yellow-100' : 'bg-red-100');
                            $textColor = $score >= 7 ? 'text-green-800' : ($score >= 4 ? 'text-yellow-800' : 'text-red-800');
                        @endphp
                        <div class="text-center p-2 rounded {{ $bgColor }}">
                            <p class="text-xs font-bold {{ $textColor }}">{{ $dim }}</p>
                            <p class="text-lg font-bold {{ $textColor }}">{{ $score }}</p>
                        </div>
                        @endforeach
                    </div>

                    @if($papi->interpretation)
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <p class="text-sm text-blue-800">{{ $papi->interpretation }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Hasil RMIB --}}
            @if($rmibResults->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762z"></path>
                    </svg>
                    Hasil RMIB (Rothwell-Miller Interest Blank)
                </h3>

                @foreach($rmibResults as $rmib)
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $rmib->test->name ?? 'Modul RMIB' }}</p>
                            <p class="text-xs text-gray-500">{{ $rmib->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Top 3 Interests --}}
                    @if($rmib->top_interests)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Top 3 Bidang Minat:</p>
                        <div class="flex gap-2">
                            @php
                                $topInterests = is_string($rmib->top_interests) ? json_decode($rmib->top_interests, true) : $rmib->top_interests;
                            @endphp
                            @if(is_array($topInterests))
                                @foreach($topInterests as $idx => $interest)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    #{{ $idx + 1 }} {{ ucfirst($interest) }}
                                </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- RMIB Category Scores --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mt-4">
                        @php
                            $categories = [
                                'outdoor' => 'Outdoor',
                                'mechanical' => 'Mechanical',
                                'computational' => 'Computational',
                                'scientific' => 'Scientific',
                                'personal' => 'Personal Contact',
                                'aesthetic' => 'Aesthetic',
                                'literary' => 'Literary',
                                'musical' => 'Musical',
                                'social' => 'Social Service',
                                'clerical' => 'Clerical',
                                'practical' => 'Practical',
                                'medical' => 'Medical'
                            ];
                        @endphp
                        @foreach($categories as $key => $label)
                        @php
                            $score = $rmib->{$key} ?? 0;
                        @endphp
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500">{{ $label }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($score / 144) * 100) }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-gray-800">{{ $score }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- No Results --}}
            @if($testResults->count() == 0 && $papiResults->count() == 0 && $rmibResults->count() == 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada hasil tes</h3>
                    <p class="text-gray-500">Peserta ini belum menyelesaikan tes apapun.</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>
