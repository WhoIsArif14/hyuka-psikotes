<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Form Masukkan Kode Tes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-800">Punya Kode Tes?</h3>
                    <p class="text-sm text-gray-600 mt-1">Masukkan kode unik untuk langsung mengerjakan tes khusus.</p>
                    <form action="{{ route('tests.find_by_code') }}" method="POST" class="mt-4 flex items-center">
                        @csrf
                        <input type="text" name="test_code" placeholder="MASUKKAN KODE" required
                               class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm font-mono uppercase focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Mulai
                        </button>
                    </form>
                    @if (session('error_code'))
                        <p class="text-sm text-red-600 mt-2">{{ session('error_code') }}</p>
                    @endif
                </div>
            </div>

            <!-- Daftar Tes Tersedia -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Pilih Tes yang Tersedia</h3>
                    <p class="text-gray-600 mb-6">Cari tes yang sesuai dengan kebutuhan Anda di bawah ini.</p>
                    
                    <!-- Form Pencarian dan Filter -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg border">
                        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Judul Tes</label>
                                <input id="search" type="text" name="search" placeholder="Contoh: Tes Logika Dasar" value="{{ request('search') }}"
                                       class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select id="category" name="category" class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Grid untuk menampilkan kartu tes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($tests as $test)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md flex flex-col">
                                <div class="p-5 flex-grow">
                                    <span class="text-sm font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">{{ $test->category->name }}</span>
                                    <h5 class="mt-3 mb-2 text-xl font-bold tracking-tight text-gray-900">{{ $test->title }}</h5>
                                    <p class="mb-3 font-normal text-gray-700">{{ Str::limit($test->description, 100) }}</p>
                                </div>
                                <div class="p-5 bg-white border-t border-gray-200 rounded-b-lg flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">
                                        @if($test->available_from)
                                            <span class="text-red-600">Terjadwal</span>
                                        @else
                                            🕒 {{ $test->duration_minutes }} Menit
                                        @endif
                                    </span>
                                    <a href="{{ route('tests.show', $test) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        Mulai Kerjakan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                                <p class="text-gray-500 text-lg">Tidak ada tes yang cocok dengan pencarian Anda.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Link Paginasi -->
                    <div class="mt-8">
                        {{ $tests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
