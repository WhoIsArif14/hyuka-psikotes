<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Sesi Tes Baru (Langkah 1 dari 3)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Kategori Tes</h3>
                    <p class="text-sm text-gray-600 mb-6">Pilih kategori utama untuk tes yang ingin Anda buat.</p>

                    <form method="POST" action="{{ route('admin.wizard.post_step1') }}">
                        @csrf
                        <div class="space-y-4">
                            @foreach ($categories as $category)
                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400">
                                    <input type="radio" name="category_id" value="{{ $category->id }}"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-gray-700 font-medium">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Lanjutkan &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>