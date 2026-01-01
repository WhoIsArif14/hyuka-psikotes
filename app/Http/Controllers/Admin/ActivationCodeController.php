<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Test;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ActivationCodeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Prefer grouping by batch_code (new batches). For legacy entries without batch_code, fallback ke kelompok waktu 1 menit.
        $groups = ActivationCode::select('batch_code', 'test_id', DB::raw('MIN(id) as first_id'))
            ->groupBy('batch_code', 'test_id')
            ->orderByDesc('first_id')
            ->get();

        $batches = $groups->map(function ($group) {
            if ($group->batch_code) {
                $codes = ActivationCode::where('batch_code', $group->batch_code)->with('test')->get();
            } else {
                // legacy fallback: find by time window around the first id
                $first = ActivationCode::find($group->first_id);
                if (!$first) return null;
                $codes = ActivationCode::where('test_id', $first->test_id)
                    ->whereBetween('created_at', [
                        $first->created_at->copy()->subMinute(),
                        $first->created_at->copy()->addMinute(),
                    ])
                    ->with('test')
                    ->get();
            }

            if ($codes->isEmpty()) return null;

            $first = $codes->first();
            $usedCount = $codes->where('status', 'Used')->count();

            return (object) [
                'id' => $first->id,
                'batch_code' => $first->batch_code,
                'batch_name' => $first->batch_name ?? ('Batch - ' . ($first->created_at->format('Y-m-d H:i'))),
                'test' => $first->test,
                'test_id' => $first->test_id,
                'created_at' => $first->created_at,
                'total_qty' => $codes->count(),
                'used_count' => $usedCount,
            ];
        })->filter()->values();

        $currentPage = $request->get('page', 1);
        $codes = new \Illuminate\Pagination\LengthAwarePaginator(
            $batches->forPage($currentPage, $perPage),
            $batches->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $tests = Test::orderBy('title')->get();

        return view('admin.codes.index', compact('codes', 'tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'quantity' => 'required|integer|min:1|max:1000',
            'batch_name' => 'nullable|string|max:255',
        ]);

        $test = Test::findOrFail($request->test_id);
        $quantity = $request->quantity;
        $batchName = trim($request->input('batch_name', '')) ?: ($test->title . ' - ' . now()->format('d M Y H:i'));
        $expiresAt = now()->addDays(365); // default 1 tahun

        // Create a deterministic batch code for this generation so all codes belong to same batch
        $batchCode = 'BATCH-' . strtoupper(Str::random(8));

        for ($i = 0; $i < $quantity; $i++) {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
            $formattedCode = substr($code, 0, 4) . '-' . substr($code, 4, 4);

            ActivationCode::create([
                'batch_code' => $batchCode,
                'batch_name' => $batchName,
                'test_id' => $test->id,
                'code' => $formattedCode,
                'status' => 'Pending',
                'expires_at' => $expiresAt,
            ]);
        }

        return redirect()->route('admin.codes.index')
            ->with('success', "Berhasil generate {$quantity} kode aktivasi untuk {$test->title}");
    }

    public function show($id)
    {
        $code = ActivationCode::with('test')->findOrFail($id);

        // Ambil semua kode yang ada dalam batch yang sama (gunakan batch_code kalau ada)
        if ($code->batch_code) {
            $batchCodes = ActivationCode::where('batch_code', $code->batch_code)
                ->with('test', 'user', 'user.testProgress')
                ->orderBy('code')
                ->get();
        } else {
            // fallback legacy: gunakan jendela waktu 1 menit
            $timeRange = $code->created_at;
            $batchCodes = ActivationCode::where('test_id', $code->test_id)
                ->whereBetween('created_at', [
                    $timeRange->copy()->subMinute(),
                    $timeRange->copy()->addMinute()
                ])
                ->with('test', 'user', 'user.testProgress')
                ->orderBy('code')
                ->get();
        }

        return view('admin.codes.show', [
            'code' => $code,
            'batchCodes' => $batchCodes,
        ]);
    }

    public function destroy($id)
    {
        try {
            $code = ActivationCode::findOrFail($id);

            // Hapus semua kode dalam batch yang sama (gunakan batch_code bila ada)
            if ($code->batch_code) {
                $codesToDelete = ActivationCode::where('batch_code', $code->batch_code)->get();
            } else {
                $timeRange = $code->created_at;
                $codesToDelete = ActivationCode::where('test_id', $code->test_id)
                    ->whereBetween('created_at', [
                        $timeRange->copy()->subMinute(),
                        $timeRange->copy()->addMinute()
                    ])
                    ->get();
            }

            $count = $codesToDelete->count();
            ActivationCode::whereIn('id', $codesToDelete->pluck('id'))->delete();

            return redirect()->route('admin.codes.index')
                ->with('success', "Berhasil menghapus {$count} kode aktivasi dalam grup yang sama.");
        } catch (\Exception $e) {
            return redirect()->route('admin.codes.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function exportBatch(Request $request, $id)
    {
        $code = ActivationCode::with('test')->findOrFail($id);

        if ($code->batch_code) {
            $batchCodes = ActivationCode::where('batch_code', $code->batch_code)
                ->with('test', 'user')
                ->orderBy('code')
                ->get();
        } else {
            $timeRange = $code->created_at;
            $batchCodes = ActivationCode::where('test_id', $code->test_id)
                ->whereBetween('created_at', [
                    $timeRange->copy()->subMinute(),
                    $timeRange->copy()->addMinute()
                ])
                ->with('test', 'user')
                ->orderBy('code')
                ->get();
        }

        // --- Sisanya sama seperti kodenya Arif ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()
            ->setCreator('Hyuka Admin')
            ->setTitle('Kode Aktivasi - ' . ($code->test->title ?? 'Export'))
            ->setSubject('Kode Aktivasi');

        $sheet->setCellValue('A1', 'DAFTAR KODE AKTIVASI');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Modul:');
        $sheet->setCellValue('B3', $code->test->title ?? 'N/A');
        $sheet->setCellValue('A4', 'Tanggal Generate:');
        $sheet->setCellValue('B4', $code->created_at->format('d-m-Y H:i'));
        $sheet->setCellValue('A5', 'Total Kode:');
        $sheet->setCellValue('B5', $batchCodes->count());

        $headerRow = 7;
        $headers = ['No', 'Kode Aktivasi', 'Status', 'Digunakan Oleh', 'Tanggal Digunakan', 'Nama Peserta'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}{$headerRow}", $header);
            $sheet->getStyle("{$col}{$headerRow}")->getFont()->setBold(true);
        }

        $row = $headerRow + 1;
        foreach ($batchCodes as $i => $item) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item->code);
            $sheet->setCellValue("C{$row}", $item->status ?? 'Pending');
            $sheet->setCellValue("D{$row}", $item->user->email ?? '-');
            $sheet->setCellValue("E{$row}", $item->used_at ? $item->used_at->format('d-m-Y H:i') : '-');
            $sheet->setCellValue("F{$row}", $item->user->name ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Kode_Aktivasi_' . ($code->test->title ?? 'Export') . '_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Update batch name for a batch (all codes with same batch_code or legacy time window)
     */
    public function updateName(Request $request, $id)
    {
        $request->validate([
            'batch_name' => 'required|string|max:255',
        ]);

        $code = ActivationCode::findOrFail($id);
        $batchName = trim($request->input('batch_name'));

        if ($code->batch_code) {
            ActivationCode::where('batch_code', $code->batch_code)->update(['batch_name' => $batchName]);
        } else {
            // legacy: update by proximate created_at window
            $timeRange = $code->created_at;
            ActivationCode::where('test_id', $code->test_id)
                ->whereBetween('created_at', [
                    $timeRange->copy()->subMinute(),
                    $timeRange->copy()->addMinute(),
                ])
                ->update(['batch_name' => $batchName]);
        }

        return redirect()->route('admin.codes.show', $id)->with('success', 'Nama batch berhasil diperbarui.');
    }

    /**
     * Reset a single activation code: clear user assignment and used_at, set status to Pending
     */
    public function reset(Request $request, $id)
    {
        $code = ActivationCode::findOrFail($id);

        // Clear usage info
        $code->status = 'Pending';
        $code->user_id = null;
        // Only touch used_at if the column exists to avoid runtime errors when migrations aren't applied yet
        if (Schema::hasColumn('activation_codes', 'used_at')) {
            $code->used_at = null;
        }
        $code->save();

        return redirect()->back()->with('success', 'Kode aktivasi berhasil di-reset.');
    }
}
