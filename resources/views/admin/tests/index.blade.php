<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Tes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.tests.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Tes Baru
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Tes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi (Menit)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tests as $test)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $test->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $test->category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $test->duration_minutes }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($test->is_published)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- BARIS KODE YANG DIPERBARUI --}}
                                        <div class="flex justify-end items-center space-x-4">
                                            <a href="{{ route('admin.tests.rules.index', $test) }}" class="text-purple-600 hover:text-purple-900 font-semibold">Interpretasi</a>
                                            <a href="{{ route('admin.tests.results', $test) }}" class="text-blue-600 hover:text-blue-900">Lihat Hasil</a>
                                            <a href="{{ route('admin.tests.questions.index', $test) }}" class="text-green-600 hover:text-green-900">Kelola Soal</a>
                                            <a href="{{ route('admin.tests.edit', $test) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.tests.destroy', $test) }}" onsubmit="return confirm('Anda yakin ingin menghapus tes ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Belum ada data tes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $tests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>