<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tes: ') . $test->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.tests.update', $test) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="test_category_id" class="block font-medium text-sm text-gray-700">Kategori Tes</label>
                            <select name="test_category_id" id="test_category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected($test->test_category_id == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700">Judul Tes</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title', $test->title) }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi/Instruksi</label>
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" rows="4">{{ old('description', $test->description) }}</textarea>
                        </div>
                        
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam Menit)</label>
                            <input id="duration_minutes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="number" name="duration_minutes" value="{{ old('duration_minutes', $test->duration_minutes) }}" required />
                        </div>

                        <div class="block mt-4">
                            <label for="is_published" class="inline-flex items-center">
                                <input id="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_published" value="1" @checked(old('is_published', $test->is_published))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Publikasikan Tes') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>