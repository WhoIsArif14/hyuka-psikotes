<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Soal untuk Alat Tes:
            <span class="text-blue-600">{{ $alatTes->name }}</span>
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-4 flex justify-between">
                    <a href="{{ route('admin.alat-tes.questions.create', $alatTes->id) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                        Tambah Soal
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pertanyaan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gambar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($questions as $question)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $question->text }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($question->image)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($question->image) }}"
                                            alt="Gambar Soal" class="h-16 w-16 object-cover">
                                    @else
                                        <span class="text-gray-400">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.questions.edit', [$alatTes->id, $question->id]) }}"
                                        class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    |
                                    <form action="{{ route('admin.questions.destroy', [$alatTes->id, $question->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Yakin ingin menghapus soal ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
