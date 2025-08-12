<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Tes Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.tests.store') }}">
                        @csrf

                        <div>
                            <label for="test_category_id" class="block font-medium text-sm text-gray-700">Kategori Tes</label>
                            <select name="test_category_id" id="test_category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700">Judul Tes</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" required />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi/Instruksi</label>
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" rows="4"></textarea>
                        </div>
                        
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam Menit)</label>
                            <input id="duration_minutes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="number" name="duration_minutes" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>