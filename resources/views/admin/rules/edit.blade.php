<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Aturan Interpretasi</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.tests.rules.update', [$test, $rule]) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="min_score" class="block font-medium text-sm text-gray-700">Skor Minimal</label>
                                <input id="min_score" class="block mt-1 w-full" type="number" name="min_score" value="{{ old('min_score', $rule->min_score) }}" required />
                            </div>
                            <div>
                                <label for="max_score" class="block font-medium text-sm text-gray-700">Skor Maksimal</label>
                                <input id="max_score" class="block mt-1 w-full" type="number" name="max_score" value="{{ old('max_score', $rule->max_score) }}" required />
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="interpretation_text" class="block font-medium text-sm text-gray-700">Teks Interpretasi</label>
                            <textarea id="interpretation_text" name="interpretation_text" rows="5" class="block mt-1 w-full">{{ old('interpretation_text', $rule->interpretation_text) }}</textarea>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tests.rules.index', $test) }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>