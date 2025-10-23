<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Hasil PAPI Kostick untuk ') }}{{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-bold mb-4">{{ __('Cakram Profil Kepribadian') }}</h3>
                <div class="my-6 flex justify-center">
                    {{-- Area Canvas untuk Chart.js --}}
                    <canvas id="papiChart" width="500" height="500"></canvas>
                </div>
                
                <h4 class="mt-8 text-lg font-bold">{{ __('Analisis Singkat') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 mt-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Tipe Profil') }}</p>
                        <p class="text-base font-semibold">{{ $result->getProfileType() }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Gaya Kerja Dominan') }}</p>
                        <p class="text-base font-semibold">{{ $result->getWorkStyle() }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Tanggal Tes') }}</p>
                        <p class="text-base font-semibold">{{ $result->test_date->format('d M Y') }}</p>
                    </div>
                </div>

                <h4 class="mt-8 text-lg font-bold">{{ __('Skor Mentah (Raw Scores)') }}</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kode') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aspek Kepribadian') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Skor (0-9)') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kategori') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- $orderedScores dan $dimensions harus dikirim dari controller --}}
                            @foreach ($orderedScores as $kode => $score)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kode }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dimensions[$kode] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $score }} / 9</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->getDimensionCategory($kode) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data ini harus di-JSON-kan dari Controller:
            const labels = @json(array_keys($orderedScores)); 
            const dataScores = @json(array_values($orderedScores)); 
            
            const data = {
                labels: labels,
                datasets: [{
                    label: '{{ __("Skor PAPI Mentah") }}',
                    data: dataScores,
                    backgroundColor: 'rgba(79, 70, 229, 0.4)', // indigo-600
                    borderColor: 'rgba(79, 70, 229, 1)',      
                    pointBackgroundColor: 'rgba(129, 140, 248, 1)',
                    pointBorderColor: '#fff',
                    borderWidth: 2,
                }]
            };

            const config = {
                type: 'radar', 
                data: data,
                options: {
                    responsive: true,
                    elements: {
                        line: {
                            borderWidth: 3
                        }
                    },
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 9, 
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            };

            const papiChart = new Chart(
                document.getElementById('papiChart'),
                config
            );
        });
    </script>
</x-admin-layout>
