<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan RMIB: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12" data-test-id="{{ $test->id }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Header & Timer --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Tabel {{ $currentTable }} dari
                                {{ $totalTables }} — {{ $question->group_title ?? 'RMIB' }}</h3>
                            <p class="text-sm text-gray-600 mt-1">Pilih ranking 1 (paling Anda sukai) sampai 12 (paling
                                tidak disukai) untuk setiap profesi.</p>
                        </div>

                        <div id="timer-display"
                            class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="time-remaining-text"></span>
                        </div>
                    </div>

                    {{-- Table of professions A-L --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-3 text-left">No</th>
                                    <th class="p-3 text-left">Kode</th>
                                    <th class="p-3 text-left">Profesi / Aktivitas</th>
                                    <th class="p-3 text-left">Ranking (1 - 12)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statements as $letter => $text)
                                    <tr class="border-t">
                                        <td class="p-3 align-top">{{ $loop->iteration }}</td>
                                        <td class="p-3 align-top font-semibold">{{ $letter }}</td>
                                        <td class="p-3 align-top">{{ $text }}</td>
                                        <td class="p-3 align-top">
                                            <select data-letter="{{ $letter }}"
                                                class="rank-select mt-1 block rounded-lg border-gray-300 shadow-sm"
                                                style="width:120px">
                                                <option value="">-- Pilih --</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ isset($savedRanks[$letter]) && $savedRanks[$letter] == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Error / Info --}}
                    <div id="rmib-message" class="mt-4 text-sm"></div>

                    {{-- Navigation Buttons --}}
                    <div class="mt-6 flex justify-between items-center">
                        <div>
                            @if ($currentTable > 1)
                                <a href="{{ route('tests.rmib.table', ['test' => $test->id, 'table' => $currentTable - 1]) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md">← Sebelumnya</a>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <button id="saveBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan
                                Tabel</button>

                            @if ($currentTable < $totalTables)
                                <a id="nextLink"
                                    href="{{ route('tests.rmib.table', ['test' => $test->id, 'table' => $currentTable + 1]) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md">Selanjutnya →</a>
                            @else
                                <button id="submitBtn" class="px-4 py-2 bg-green-600 text-white rounded-md">Kirim
                                    Seluruh Tes</button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Timer
        let timeRemaining = {{ $timeRemaining }}; // seconds
        const timeDisplay = document.getElementById('time-remaining-text');

        function formatTime(sec) {
            const m = Math.floor(sec / 60).toString().padStart(2, '0');
            const s = (sec % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        }

        function startTimer() {
            timeDisplay.textContent = formatTime(timeRemaining);
            const t = setInterval(() => {
                timeRemaining = Math.max(0, timeRemaining - 1);
                timeDisplay.textContent = formatTime(timeRemaining);
                if (timeRemaining <= 0) {
                    clearInterval(t);
                    // Auto-submit current table (best effort)
                    document.getElementById('saveBtn').click();
                }
            }, 1000);
        }
        startTimer();

        // Save logic
        const saveBtn = document.getElementById('saveBtn');
        const submitBtn = document.getElementById('submitBtn');
        const messageDiv = document.getElementById('rmib-message');

        function collectRanks() {
            const selects = document.querySelectorAll('.rank-select');
            const ranks = {};
            selects.forEach(s => {
                const letter = s.dataset.letter;
                const val = s.value ? parseInt(s.value, 10) : null;
                ranks[letter] = val;
            });
            return ranks;
        }

        function validateRanks(ranks) {
            const values = Object.values(ranks);
            // Check all present
            if (values.some(v => v === null)) {
                return 'Semua profesi harus diberi ranking 1 sampai 12.';
            }
            // Check uniqueness
            const unique = new Set(values);
            if (unique.size !== values.length) {
                return 'Setiap angka ranking hanya boleh digunakan satu kali.';
            }
            // Check range
            for (const v of values) {
                if (v < 1 || v > 12) return 'Ranking harus antara 1 dan 12.';
            }
            return null;
        }

        async function saveTable() {
            const ranks = collectRanks();
            const err = validateRanks(ranks);
            if (err) {
                messageDiv.innerHTML = `<p class='text-red-600'>⚠️ ${err}</p>`;
                return;
            }

            saveBtn.disabled = true;
            messageDiv.innerHTML = `<p class='text-gray-600'>Menyimpan...</p>`;

            try {
                const res = await fetch(
                    "{{ route('tests.rmib.save', ['test' => $test->id, 'table' => $currentTable]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_id: {{ $question->id }},
                            ranks
                        })
                    });

                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Terjadi kesalahan');

                messageDiv.innerHTML = `<p class='text-green-600'>✅ Tabel disimpan.</p>`;
            } catch (e) {
                messageDiv.innerHTML = `<p class='text-red-600'>❌ ${e.message}</p>`;
            } finally {
                saveBtn.disabled = false;
            }
        }

        if (saveBtn) saveBtn.addEventListener('click', saveTable);

        // Submit entire RMIB test
        if (submitBtn) {
            submitBtn.addEventListener('click', async function() {
                // First try to save current table
                await saveTable();
                if (messageDiv.innerHTML.includes('✅')) {
                    // Send submit request
                    submitBtn.disabled = true;
                    messageDiv.innerHTML = `<p class='text-gray-600'>Mengirim jawaban...</p>`;
                    try {
                        const res = await fetch("{{ route('tests.rmib.submit', ['test' => $test->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        });

                        if (!res.ok) {
                            const d = await res.text();
                            throw new Error(d || 'Gagal mengirim tes');
                        }

                        window.location.href = "{{ route('tests.dashboard', $test->id) }}";
                    } catch (e) {
                        messageDiv.innerHTML = `<p class='text-red-600'>❌ ${e.message}</p>`;
                        submitBtn.disabled = false;
                    }
                }
            });
        }
    </script>
</x-app-layout>
