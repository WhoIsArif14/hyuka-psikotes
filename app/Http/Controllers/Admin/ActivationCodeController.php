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

class ActivationCodeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Ambil daftar kode aktivasi unik berdasarkan waktu pembuatan (1 menit dianggap satu batch)
        $distinctGroups = ActivationCode::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as group_time'),
                'test_id'
            )
            ->groupBy('group_time', 'test_id')
            ->orderByDesc('group_time')
            ->get();

        $batches = $distinctGroups->map(function ($group) {
            $codes = ActivationCode::where('test_id', $group->test_id)
                ->whereBetween('created_at', [
                    now()->parse($group->group_time),
                    now()->parse($group->group_time)->addMinute(),
                ])
                ->with('test')
                ->get();

            if ($codes->isEmpty()) {
                return null;
            }

            $first = $codes->first();
            $usedCount = $codes->where('status', 'Used')->count();

            return (object) [
                'id' => $first->id,
                'batch_name' => $first->batch_name ?? 'Batch ' . $group->group_time,
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
        $batchName = $request->batch_name ?? ($test->title . ' - ' . now()->format('d M Y H:i'));
        $expiresAt = now()->addDays(365); // default 1 tahun

        for ($i = 0; $i < $quantity; $i++) {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
            $formattedCode = substr($code, 0, 4) . '-' . substr($code, 4, 4);

            ActivationCode::create([
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

        // Ambil semua kode yang dibuat dalam selisih waktu 1 menit dari kode ini (anggap satu batch)
        $timeRange = $code->created_at;
        $batchCodes = ActivationCode::where('test_id', $code->test_id)
            ->whereBetween('created_at', [
                $timeRange->copy()->subMinute(),
                $timeRange->copy()->addMinute()
            ])
            ->with('test', 'user')
            ->orderBy('code')
            ->get();

        return view('admin.codes.show', [
            'code' => $code,
            'batchCodes' => $batchCodes,
        ]);
    }

    public function destroy($id)
    {
        try {
            $code = ActivationCode::findOrFail($id);

            // Hapus semua kode yang dibuat dalam waktu berdekatan (anggap satu batch)
            $timeRange = $code->created_at;
            $codesToDelete = ActivationCode::where('test_id', $code->test_id)
                ->whereBetween('created_at', [
                    $timeRange->copy()->subMinute(),
                    $timeRange->copy()->addMinute()
                ])
                ->get();

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

        $timeRange = $code->created_at;
        $batchCodes = ActivationCode::where('test_id', $code->test_id)
            ->whereBetween('created_at', [
                $timeRange->copy()->subMinute(),
                $timeRange->copy()->addMinute()
            ])
            ->with('test', 'user')
            ->orderBy('code')
            ->get();

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
}
