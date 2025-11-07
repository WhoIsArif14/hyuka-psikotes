<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheatingLog;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheatingDetectionController extends Controller
{
    /**
     * Log pelanggaran kecurangan
     */
    public function logViolation(Request $request)
    {
        try {
            $validated = $request->validate([
                'test_id' => 'required|exists:alat_tes,id',
                'violation_type' => 'required|in:TAB_SWITCH,SCREENSHOT,COPY_PASTE,RIGHT_CLICK,DEVELOPER_TOOLS,WINDOW_BLUR,FULLSCREEN_EXIT',
                'description' => 'nullable|string',
                'test_result_id' => 'nullable|exists:test_results,id',
            ]);

            $userId = auth()->id();

            // Log violation
            $cheatingLog = CheatingLog::logViolation(
                $userId,
                $validated['test_id'],
                $validated['violation_type'],
                $validated['description'] ?? null,
                $validated['test_result_id'] ?? null
            );

            // Hitung total pelanggaran
            $totalViolations = CheatingLog::getViolationCount($userId, $validated['test_id']);

            // Cek apakah sudah melebihi batas
            $maxViolations = 5; // Batas maksimal pelanggaran
            $isBlocked = $totalViolations >= $maxViolations;

            // Jika melebihi batas, tandai test result sebagai curang
            if ($isBlocked && isset($validated['test_result_id'])) {
                $testResult = TestResult::find($validated['test_result_id']);
                if ($testResult) {
                    $testResult->update([
                        'is_cheating' => true,
                        'cheating_notes' => "Terdeteksi {$totalViolations} pelanggaran: {$validated['violation_type']}"
                    ]);
                }
            }

            Log::warning('Cheating detected', [
                'user_id' => $userId,
                'test_id' => $validated['test_id'],
                'violation_type' => $validated['violation_type'],
                'total_violations' => $totalViolations,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggaran berhasil dicatat',
                'data' => [
                    'total_violations' => $totalViolations,
                    'max_violations' => $maxViolations,
                    'is_blocked' => $isBlocked,
                    'warning_message' => $isBlocked 
                        ? 'Tes Anda dibatalkan karena terdeteksi kecurangan!' 
                        : "Peringatan! Terdeteksi {$totalViolations} pelanggaran. Batas maksimal: {$maxViolations}",
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log cheating violation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pelanggaran'
            ], 500);
        }
    }

    /**
     * Cek status pelanggaran user
     */
    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|exists:alat_tes,id',
        ]);

        $userId = auth()->id();
        $totalViolations = CheatingLog::getViolationCount($userId, $validated['test_id']);
        $maxViolations = 5;
        $isBlocked = $totalViolations >= $maxViolations;

        return response()->json([
            'success' => true,
            'data' => [
                'total_violations' => $totalViolations,
                'max_violations' => $maxViolations,
                'is_blocked' => $isBlocked,
                'remaining_chances' => max(0, $maxViolations - $totalViolations),
            ]
        ]);
    }
}