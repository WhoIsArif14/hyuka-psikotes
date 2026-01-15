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
                        <h2 class="text-2xl font-bold text-gray-800">Detail Modul</h2>
                        <h5 class="text-gray-600 mt-1">{{ $test->title }}</h5>
                        <nav class="text-sm breadcrumbs mt-2">
                            <ul class="flex space-x-2 text-gray-600">
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                                </li>
                                <li>/</li>
                                <li><a href="{{ route('admin.tests.index') }}" class="hover:text-blue-600">Modul Tes</a>
                                </li>
                                <li>/</li>
                                <li class="text-gray-800 font-semibold">Detail Modul</li>
                            </ul>
                        </nav>
                        <p class="mt-2 text-sm text-gray-500">Catatan: Halaman ini menampilkan konfigurasi dan isi modul
                            yang telah dibuat (alat tes, durasi, jumlah soal). Untuk melihat hasil peserta, silakan buka
                            menu <span class="font-semibold">Laporan</span> di sidebar.</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.tests.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                            <i class="bi bi-arrow-left mr-2"></i> Kembali ke Daftar Modul
                        </a>
                        <a href="{{ route('admin.tests.edit', $test->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                            <i class="bi bi-pencil mr-2"></i> Edit Modul
                        </a>
                        <a href="{{ route('admin.reports.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                            <i class="bi bi-file-earmark-text mr-2"></i> Menu Laporan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Statistik Modul --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Total Alat Tes</h6>
                        <h3 class="text-3xl font-bold text-blue-600">
                            {{ $statistics['total_alat_tes'] ?? $test->AlatTes->count() }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Total Soal</h6>
                        <h3 class="text-3xl font-bold text-indigo-600">{{ $statistics['total_questions'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Total Durasi (menit)</h6>
                        <h3 class="text-3xl font-bold text-green-600">
                            {{ $statistics['total_duration'] ?? $test->duration_minutes }}</h3>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h6 class="text-gray-600 text-xs mb-2">Peserta Terdaftar</h6>
                        <h3 class="text-3xl font-bold text-teal-600">{{ $statistics['total_participants'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            {{-- Detail Modul & Alat Tes --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Daftar Alat Tes</h3>

                    @if ($alatTesWithQuestions && $alatTesWithQuestions->count() > 0)
                        <div class="space-y-4">
                            @foreach ($alatTesWithQuestions as $index => $alat)
                                <div class="border rounded-lg p-4 flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-md font-semibold text-gray-800">{{ $index + 1 }}.
                                                    {{ $alat->name }}</h4>
                                                @if ($alat->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $alat->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-500">{{ $alat->duration_minutes }} menit
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $alat->total_questions ?? 0 }}
                                                    soal</div>
                                            </div>
                                        </div>
                                        @if (!empty($alat->example_questions))
                                            <div class="mt-3 text-sm text-gray-700">
                                                <strong>Soal Contoh:</strong>
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($alat->example_questions as $index => $q)
                                                        <div class="py-1 pl-2 border-l-2 border-gray-300">
                                                            @if (is_array($q))
                                                                {{-- Coba berbagai key yang mungkin ada --}}
                                                                @if (isset($q['text']))
                                                                    <span class="font-medium">{{ $index + 1 }}.</span> {{ $q['text'] }}
                                                                @elseif (isset($q['question']))
                                                                    <span class="font-medium">{{ $index + 1 }}.</span> {{ $q['question'] }}
                                                                @elseif (isset($q['question_text']))
                                                                    <span class="font-medium">{{ $index + 1 }}.</span> {{ $q['question_text'] }}
                                                                @else
                                                                    {{-- Tampilkan array dalam format yang lebih rapi --}}
                                                                    <div class="text-gray-600">
                                                                        <span class="font-medium">{{ $index + 1 }}.</span>
                                                                        <div class="ml-4 mt-1">
                                                                            @foreach ($q as $key => $value)
                                                                                <div class="text-xs">
                                                                                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <span class="font-medium">{{ $index + 1 }}.</span> {{ $q }}
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex flex-col gap-2">
                                        <a href="{{ route('admin.alat-tes.edit', $alat->id) }}"
                                            class="px-3 py-1 bg-green-500 text-white rounded-md text-xs">Edit Alat</a>
                                        <a href="{{ route('admin.reports.index') }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-xs"
                                            title="Hasil peserta tersedia di menu Laporan">Lihat Laporan</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-yellow-800">Belum ada alat tes yang terdaftar di modul ini.</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Informasi Modul</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $test->description }}</p>

                    <div class="space-y-3 text-sm text-gray-700">
                        <div><strong>Kategori:</strong> {{ $test->category->name ?? '-' }}</div>
                        <div><strong>Jenjang:</strong> {{ $test->jenjang->name ?? '-' }}</div>
                        <div><strong>Durasi:</strong> {{ $test->duration_minutes ?? '-' }} menit</div>
                        <div><strong>Status:</strong>
                            @if ($test->is_published)
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                            @endif
                        </div>
                        <div><strong>Dibuat:</strong> {{ $test->created_at->format('d M Y') }}</div>
                        <div><strong>Terakhir Diubah:</strong> {{ $test->updated_at->format('d M Y') }}</div>
                    </div>

                    {{-- Optional: tombol untuk menambah alat tes ke modul --}}
                    <div class="mt-6">
                        <a href="{{ route('admin.tests.edit', $test->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Atur
                            Alat Tes / Ubah Modul</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
