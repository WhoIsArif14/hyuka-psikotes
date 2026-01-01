<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Peserta Tes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Hasil Peserta Tes</h2>
                        <h5 class="text-gray-600 mt-1">{{ $test->title }}</h5>
                        <nav class="text-sm breadcrumbs mt-2">
                            <ul class="flex space-x-2 text-gray-600">
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                                </li>
                                <li>/</li>
                                <li><a href="{{ route('admin.tests.index') }}" class="hover:text-blue-600">Modul Tes</a>
                                </li>
                                <li>/</li>
                                <li><a href="{{ route('admin.tests.show', $test->id) }}"
                                        class="hover:text-blue-600">{{ $test->title }}</a></li>
                                <li>/</li>
                                <li class="text-gray-800 font-semibold">Hasil Peserta</li>
                            </ul>
                        </nav>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.tests.show', $test->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                            <i class="bi bi-arrow-left mr-2"></i> Kembali ke Detail
                        </a>
                        <a href="{{ route('admin.tests.export', $test->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                            <i class="bi bi-file-excel mr-2"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- Statistik Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Total Peserta</h6>
                        <h3 class="text-3xl font-bold text-blue-600">{{ $statistics['total_participants'] }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Selesai</h6>
                        <h3 class="text-3xl font-bold text-green-600">{{ $statistics['completed'] }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Dalam Progress</h6>
                        <h3 class="text-3xl font-bold text-yellow-600">{{ $statistics['in_progress'] }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Rata-rata Skor</h6>
                        <h3 class="text-3xl font-bold text-indigo-600">{{ $statistics['average_score'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Skor Tertinggi</h6>
                        <h3 class="text-3xl font-bold text-green-600">{{ $statistics['highest_score'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Skor Terendah</h6>
                        <h3 class="text-3xl font-bold text-red-600">{{ $statistics['lowest_score'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            {{-- Tabel Hasil Peserta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-blue-600 text-white">
                    <h5 class="text-lg font-semibold"><i class="bi bi-people mr-2"></i> Daftar Hasil Peserta</h5>
                </div>
                <div class="p-6">
                    @if ($results->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Peserta</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. HP</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pendidikan</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jurusan</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Skor</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IQ</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($results as $index => $result)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $results->firstItem() + $index }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <strong
                                                    class="text-gray-800">{{ $result->participant_name ?? ($result->user->name ?? '-') }}</strong>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $result->participant_email ?? ($result->user->email ?? '-') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $result->phone_number ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $result->education ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $result->major ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span
                                                    class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                                    {{ $result->score ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span
                                                    class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                                                    {{ $result->iq ?? '-' }}
                                                </span>
                                                @if ($result->iq)
                                                    <div class="text-xs text-gray-600 mt-1">
                                                        {{ $result->iq_interpretation }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if ($result->finished_at)
                                                    <span
                                                        class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                                        <i class="bi bi-check-circle"></i> Selesai
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                                        <i class="bi bi-clock"></i> Progress
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $result->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <a href="{{ route('admin.reports.pdf', $result->id) }}"
                                                    class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm"
                                                    title="Lihat Detail Hasil">
                                                    <i class="bi bi-file-text mr-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $results->links() }}
                        </div>
                    @else
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 text-center">
                            <i class="bi bi-info-circle text-blue-600 text-2xl"></i>
                            <p class="text-blue-700 mt-2">Belum ada peserta yang mengerjakan tes ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
