<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Peserta</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <form action="{{ route('admin.peserta.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari nama atau email peserta..." value="{{ request('search') }}" class="w-full md:w-1/3 border-gray-300 rounded-md shadow-sm">
                            <button type="submit" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-md">Cari</button>
                        </form>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                                <th class="relative px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($peserta as $user)
                                <tr>
                                    <td class="px-6 py-4">{{ $user->name }}</td>
                                    <td class="px-6 py-4">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        {{-- PERUBAHAN ADA DI BAGIAN INI --}}
                                        <div class="flex justify-end space-x-4">
                                            <a href="{{ route('admin.peserta.show', $user) }}" class="text-blue-600 hover:text-blue-900">Lihat Riwayat</a>
                                            
                                            {{-- TOMBOL EDIT BARU --}}
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                            <form method="POST" action="{{ route('admin.peserta.destroy', $user) }}" onsubmit="return confirm('Anda yakin ingin menghapus peserta ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data peserta ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $peserta->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>