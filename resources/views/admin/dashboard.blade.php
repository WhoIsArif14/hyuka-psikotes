<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Grid untuk Kartu Statistik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kuota Peserta -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Kuota Peserta Tes</h4>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProcess) }}</p>
                        <p class="ml-2 text-sm text-gray-500">/ {{ number_format($kuotaPeserta) }} diproses</p>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Sisa Kuota: <span class="font-semibold">{{ number_format($sisaKuotaPeserta) }}</span></p>
                </div>

                <!-- Kuota Psikogram -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Psikogram Builder</h4>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($psikogramCreated) }}</p>
                        <p class="ml-2 text-sm text-gray-500">/ {{ number_format($kuotaPsikogram) }} dibuat</p>
                    </div>
                     <p class="text-sm text-gray-600 mt-1">Sisa Kuota: <span class="font-semibold">{{ number_format($sisaKuotaPsikogram) }}</span></p>
                </div>

                <!-- Masa Berakhir Langganan -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Langganan</h4>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ $expiredInDays }}</p>
                         <p class="ml-2 text-sm text-gray-500">Hari lagi</p>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Akan berakhir</p>
                </div>
            </div>

            <!-- Rangkuman Pelaksanaan Psikotes -->
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-green-800">Rangkuman Pelaksanaan Psikotes</h3>
                    <div class="mt-4 flow-root">
                        <div class="-my-2 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <table class="min-w-full divide-y divide-green-200">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Nama Tes</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Pengerjaan</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse ($rangkumanTes as $tes)
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                    {{-- Link untuk melihat detail modul (bukan hasil peserta) --}}
                                                    <a href="{{ route('admin.tests.show', $tes) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $tes->title }}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $tes->test_results_count }} Peserta</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    @if ($tes->is_published)
                                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Published</span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Draft</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-sm text-gray-500">Belum ada tes yang dikerjakan oleh peserta.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>