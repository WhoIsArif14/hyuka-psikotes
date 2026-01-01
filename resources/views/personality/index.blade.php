@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Tes Kepribadian</h1>

        <ul class="space-y-3">
            @foreach ($tests as $t)
                <li class="p-4 border rounded">
                    <h2 class="font-semibold">{{ $t->title }}</h2>
                    <p class="text-sm text-gray-600">{{ $t->description }}</p>
                    <a href="{{ route('personality.show', $t->id) }}" class="inline-block mt-2 text-blue-600">Mulai Tes</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
