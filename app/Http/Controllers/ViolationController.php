<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestViolation;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ViolationController extends Controller
{
    /**
     * Log violation dari client-side
     */
    public function logViolation(Request $request)
    {
        try {
            $request->validate([
                'test_id' => 'required|integer',
                'type' => 'required|string',
                'details' => 'nullable|string',
                'timestamp' => 'required|string',
            ]);

            $user = Auth::user();

            // Simpan violation ke database
            $violation = TestViolation::create([
                'user_id' => $user->id,
                'test_id' => $request->test_id,
                'violation_type' => $request->type,
                'details' => $request->details,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'occurred_at' => $request->timestamp,
            ]);

            // Log untuk monitoring
            Log::warning('Test violation detected', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'test_id' => $request->test_id,
                'violation_type' => $request->type,
                'details' => $request->details
            ]);

            // Hitung total pelanggaran untuk test session ini (dalam 2 jam terakhir)
            $totalViolations = TestViolation::where('user_id', $user->id)
                ->where('test_id', $request->test_id)
                ->where('created_at', '>=', now()->subHours(2))
                ->count();

            // Jika sudah 3 kali pelanggaran, return terminated
            if ($totalViolations >= 3) {
                return response()->json([
                    'status' => 'terminated',
                    'message' => 'Test dihentikan karena terlalu banyak pelanggaran',
                    'total_violations' => $totalViolations,
                    'redirect' => route('test.terminated')
                ], 403);
            }

            return response()->json([
                'status' => 'logged',
                'total_violations' => $totalViolations,
                'message' => 'Violation logged successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error logging violation: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log violation'
            ], 500);
        }
    }

    /**
     * Heartbeat untuk tracking user masih aktif
     */
    public function testHeartbeat(Request $request)
    {
        try {
            $request->validate([
                'test_id' => 'required|integer',
                'violations' => 'nullable|integer',
                'tab_switches' => 'nullable|integer',
            ]);

            $user = Auth::user();

            // Simple heartbeat - hanya return status OK
            // Bisa ditambahkan logic untuk update last_activity jika diperlukan
            
            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toIso8601String()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Heartbeat error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Halaman test terminated
     */
    public function terminated()
    {
        // Ambil data pelanggaran terakhir user
        $violations = TestViolation::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subHours(2))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('tests.terminated', compact('violations'));
    }

    /**
     * Admin: Lihat semua violations
     */
    public function adminIndex(Request $request)
    {
        $query = TestViolation::with(['user', 'test'])
            ->orderBy('created_at', 'desc');

        // Filter by violation type
        if ($request->has('type') && $request->type != '') {
            $query->where('violation_type', $request->type);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        $violations = $query->paginate(50);

        // Get violation type statistics
        $violationStats = TestViolation::selectRaw('violation_type, COUNT(*) as count')
            ->groupBy('violation_type')
            ->get()
            ->pluck('count', 'violation_type');

        return view('admin.violations.index', compact('violations', 'violationStats'));
    }

    /**
     * Admin: Lihat detail violations untuk user tertentu
     */
    public function adminUserViolations($userId)
    {
        $violations = TestViolation::with('test')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $user = \App\Models\User::findOrFail($userId);

        return view('admin.violations.user', compact('violations', 'user'));
    }

    /**
     * Admin: Hapus violation (jika false positive)
     */
    public function adminDelete($id)
    {
        $violation = TestViolation::findOrFail($id);
        $violation->delete();

        return redirect()->back()->with('success', 'Violation berhasil dihapus');
    }

    /**
     * Admin: Export violations report to CSV
     */
    public function export(Request $request)
    {
        $query = TestViolation::with(['user', 'test'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('type') && $request->type != '') {
            $query->where('violation_type', $request->type);
        }
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        $violations = $query->get();

        $filename = 'violations_report_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($violations) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Waktu',
                'User ID',
                'Nama User',
                'Email',
                'Test ID',
                'Nama Test',
                'Tipe Pelanggaran',
                'Detail',
                'IP Address',
                'User Agent'
            ]);

            // Data rows
            foreach ($violations as $violation) {
                fputcsv($file, [
                    $violation->created_at->format('Y-m-d H:i:s'),
                    $violation->user_id,
                    $violation->user->name ?? 'N/A',
                    $violation->user->email ?? 'N/A',
                    $violation->test_id,
                    $violation->test->title ?? 'N/A',
                    $violation->getTypeLabel(),
                    $violation->details,
                    $violation->ip_address,
                    $violation->user_agent
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}