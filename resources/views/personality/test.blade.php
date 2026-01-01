@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ $personalityTest->title }}</h1>
        <p class="text-sm text-gray-600 mb-4">{{ $personalityTest->description }}</p>

        <form method="POST" action="{{ route('personality.submit', $personalityTest->id) }}">
            @csrf

            @foreach ($questions as $q)
                <div class="mb-4 p-3 border rounded">
                    <div class="mb-2">{{ $loop->iteration }}. {{ $q->question }}</div>
                    <div class="space-x-2">
                        @foreach ($q->options as $opt)
                            <label class="inline-flex items-center mr-4">
                                <input type="radio" name="q_{{ $q->id }}" value="{{ $opt['value'] }}" required
                                    class="mr-1">
                                <span class="text-sm">{{ $opt['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button class="px-4 py-2 bg-blue-600 text-white rounded">Kirim Jawaban</button>
        </form>
    </div>
@endsection
