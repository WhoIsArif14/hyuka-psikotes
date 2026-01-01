<?php

namespace App\Services;

use App\Models\Test;
use App\Models\PapiResult;
use App\Models\RmibResult;
use App\Models\PauliResult;
use App\Models\PersonalityResult;
use App\Models\User;

class ModuleReportService
{
    /**
     * Generate a report summary for a given Test (module) and user (optional, defaults to current auth user)
     * Returns array with each alat_tes section: ['title' => ..., 'summary' => ..., 'details' => []]
     */
    public function generate(Test $test, ?User $user = null): array
    {
        $user = $user ?: auth()->user();

        $report = [
            'module' => $test->title,
            'description' => $test->description,
            'generated_at' => now()->toDateTimeString(),
            'sections' => [],
        ];

        foreach ($test->alatTes as $alat) {
            $title = $alat->name ?? "Alat Tes #{$alat->id}";
            $slug = strtolower($alat->slug ?? '');

            // Dispatch per known tool
            if (str_contains($slug, 'papi') || str_contains(strtolower($title), 'papi')) {
                $section = $this->generatePapiSection($alat->id, $user);
            } elseif (str_contains($slug, 'rmib') || str_contains(strtolower($title), 'rmib')) {
                $section = $this->generateRmibSection($alat->id, $user);
            } elseif (str_contains($slug, 'pauli') || str_contains(strtolower($title), 'pauli')) {
                $section = $this->generatePauliSection($alat->id, $user);
            } elseif (str_contains($slug, 'personality') || str_contains(strtolower($title), 'kepribadian')) {
                $section = $this->generatePersonalitySection($alat->id, $user);
            } else {
                // Fallback: try to find a TestResult record by alat_tes_id
                $section = [
                    'title' => $title,
                    'summary' => 'Tipe alat tes tidak dikenali. Tidak ada ringkasan otomatis.',
                    'details' => []
                ];
            }

            $report['sections'][] = $section;
        }

        return $report;
    }

    protected function generatePapiSection($alatTesId, User $user)
    {
        $result = PapiResult::where('user_id', $user->id)
            ->where('participant_number', $user->id)
            ->latest()
            ->first();

        if (!$result) {
            return [
                'title' => 'PAPI Kostick',
                'summary' => 'Belum ada hasil PAPI untuk peserta ini.',
                'details' => []
            ];
        }

        $top = $result->getHighestDimensions(3);
        $profile = $result->getProfileType();
        $workstyle = $result->getWorkStyle();

        $summary = "PAPI: tipe profil utama = {$profile}. Nilai tertinggi: ";
        $parts = [];
        foreach ($top as $dim => $score) {
            $parts[] = "{$dim} ({$score})";
        }
        $summary .= implode(', ', $parts) . ". Work style: {$workstyle}.";

        return [
            'title' => 'PAPI Kostick',
            'summary' => $summary,
            'details' => [
                'ordered_scores' => $result->getOrderedScores(),
                'top_dimensions' => $top,
                'profile' => $profile,
                'work_style' => $workstyle,
            ],
        ];
    }

    protected function generateRmibSection($alatTesId, User $user)
    {
        // Find latest RMIB result for this user and alat
        $result = RmibResult::where('user_id', $user->id)
            ->where('alat_tes_id', $alatTesId)
            ->latest()
            ->first();

        if (!$result) {
            return [
                'title' => 'RMIB (Minat)',
                'summary' => 'Belum ada hasil RMIB untuk peserta ini.',
                'details' => []
            ];
        }

        $top = $result->getTopInterests();
        $summary = "RMIB: Top interests: ";
        $labels = array_map(fn($t) => "$t[name] ({$t['score']})", $top);
        $summary .= implode(', ', $labels) . ".";

        return [
            'title' => 'RMIB - Minat',
            'summary' => $summary,
            'details' => [
                'scores' => $result->getScoresArray(),
                'top_interests' => $top,
            ],
        ];
    }

    protected function generatePauliSection($alatTesId, User $user)
    {
        $result = \App\Models\PauliResult::where('user_id', $user->id)
            ->where('pauli_test_id', $alatTesId)
            ->latest()
            ->first();

        if (!$result) {
            return [
                'title' => 'Pauli',
                'summary' => 'Belum ada hasil Pauli untuk peserta ini.',
                'details' => []
            ];
        }

        $accuracy = $result->accuracy ?? 0;
        $avgSpeed = $result->average_speed ?? 0;

        $grade = $this->interpretPauli($accuracy, $avgSpeed);
        $summary = "Pauli: Akurasi {$accuracy}%, Kecepatan rata-rata {$avgSpeed}. Interpretasi: {$grade}.";

        return [
            'title' => 'Pauli',
            'summary' => $summary,
            'details' => [
                'accuracy' => $accuracy,
                'average_speed' => $avgSpeed,
                'total_answers' => $result->total_answers,
            ],
        ];
    }

    protected function interpretPauli($accuracy, $avgSpeed)
    {
        if ($accuracy >= 90 && $avgSpeed <= 2.0) {
            return 'Sangat Baik (cepat dan akurat)';
        }
        if ($accuracy >= 80) {
            return 'Baik (akurasi baik)';
        }
        if ($accuracy >= 60) {
            return 'Cukup (perlu peningkatan akurasi atau kecepatan)';
        }
        return 'Perlu Latihan (akurasi rendah)';
    }

    protected function generatePersonalitySection($alatTesId, User $user)
    {
        $result = PersonalityResult::where('user_id', $user->id)
            ->where('personality_test_id', $alatTesId)
            ->latest()
            ->first();

        if (!$result) {
            return [
                'title' => 'Tes Kepribadian',
                'summary' => 'Belum ada hasil tes kepribadian untuk peserta ini.',
                'details' => []
            ];
        }

        $max = count($result->details ?? []) * 5;
        $percent = $max ? round($result->score / $max * 100) : 0;

        $interpret = $result->interpretation ?? $this->interpretPersonalityPercent($percent);

        $summary = "Kepribadian: skor total {$result->score} ({$percent}%). Interpretasi: {$interpret}.";

        return [
            'title' => 'Tes Kepribadian',
            'summary' => $summary,
            'details' => [
                'score' => $result->score,
                'percent' => $percent,
                'interpretation' => $interpret,
            ],
        ];
    }

    protected function interpretPersonalityPercent($percent)
    {
        if ($percent >= 75) return 'Skor tinggi: cenderung ekstrovert/aktif';
        if ($percent >= 50) return 'Skor sedang: keseimbangan';
        return 'Skor rendah: cenderung introvert/tenang';
    }
}
