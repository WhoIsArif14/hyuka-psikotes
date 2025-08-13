<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-g">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hyuka Psikotes - Temukan Potensi Diri Anda</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-red-500 selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            {{-- Logo bisa ditaruh di sini --}}
                            <h1 class="text-2xl font-bold text-gray-800">Hyuka</h1>
                        </div>
                        <nav class="flex flex-1 justify-end">
                            @if (Route::has('login'))
                                @auth
                                    <a
                                        href="{{ url('/dashboard') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                                    >
                                        Dashboard
                                    </a>
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                                    >
                                        Masuk
                                    </a>

                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                                        >
                                            Daftar
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </nav>
                    </header>

                    <main class="mt-20">
                        <div class="text-center">
                            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 tracking-tight">
                                Temukan Potensi Diri Anda Bersama <span class="text-blue-600">Hyuka</span>.
                            </h1>
                            <p class="mt-6 text-lg text-gray-600 max-w-3xl mx-auto">
                                Platform psikotes online yang dirancang untuk membantu Anda memahami kepribadian, kekuatan, dan area pengembangan diri Anda dengan mudah dan akurat.
                            </p>
                            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Mulai Sekarang
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Sudah Punya Akun?
                                </a>
                            </div>
                        </div>
                    </main>

                    <footer class="py-16 text-center text-sm text-black/50">
                        Hyuka Psikotes &copy; {{ date('Y') }}
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>