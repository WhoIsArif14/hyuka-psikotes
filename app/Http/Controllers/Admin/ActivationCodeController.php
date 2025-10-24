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

class ActivationCodeController extends Controller
{
    public function index()
    {
        $codes = ActivationCode::with('test')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $tests = Test::orderBy('title')->get();
        
        return view('admin.codes.index', compact('codes', 'tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'quantity' => 'required|integer|min:1|max:1000',
            'batch_name' => 'nullable|string|max:255',
            // Tambahan: Anda bisa menambahkan 'expiry_days' di sini jika ingin mengontrol masa kadaluarsa
        ]);

        $test = Test::findOrFail($request->test_id);
        $quantity = $request->quantity;
        $batchId = uniqid('batch_');
        $batchName = $request->batch_name ?? ($test->title . ' - ' . now()->format('d M Y'));

        // SOLUSI: Tentukan tanggal kadaluarsa (expires_at)
        // Disetel 1 tahun (365 hari) dari sekarang sebagai nilai default yang aman.
        $expiresAt = now()->addDays(365); 

        for ($i = 0; $i < $quantity; $i++) {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
            
            // Format: XXXX-XXXX
            $formattedCode = substr($code, 0, 4) . '-' . substr($code, 4, 4);

            ActivationCode::create([
                'batch_id' => $batchId,
                'batch_name' => $batchName,
                'test_id' => $request->test_id,
                'code' => $formattedCode,
                'status' => 'Pending',
                // PERBAIKAN UTAMA: Menyertakan nilai untuk kolom yang wajib (NOT NULL)
                'expires_at' => $expiresAt, 
            ]);
        }

        return redirect()->route('admin.codes.index')
            ->with('success', "Berhasil generate {$quantity} kode aktivasi untuk {$test->title}");
    }

    public function show($id)
    {
        $code = ActivationCode::with('test')->findOrFail($id);
        
        // Jika ada parameter batch, ambil semua kode dalam batch tersebut
        if (request('batch')) {
            $batchKey = request('batch');
            
            // Cari semua kode dengan batch_id yang sama
            if (isset($code->batch_id)) {
                $batchCodes = ActivationCode::where('batch_id', $code->batch_id)
                    ->with('test', 'user')
                    ->orderBy('code')
                    ->get();
            } else {
                // Fallback: group by test_id dan waktu generate yang sama (dalam 1 menit)
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
        } else {
            $batchCodes = collect([$code]);
        }
        
        return view('admin.codes.show', [
            'code' => $code,
            'batchCodes' => $batchCodes,
        ]);
    }

    public function destroy($id)
    {
        $code = ActivationCode::findOrFail($id);
        
        // Jika ada batch_ids di request, hapus multiple
        if (request()->has('batch_ids')) {
            $batchIds = json_decode(request('batch_ids'), true);
            ActivationCode::whereIn('id', $batchIds)->delete();
            
            return redirect()->route('admin.codes.index')
                ->with('success', 'Batch kode aktivasi berhasil dihapus.');
        }
        
        $code->delete();
        
        return redirect()->route('admin.codes.index')
            ->with('success', 'Kode aktivasi berhasil dihapus.');
    }

    public function exportBatch(Request $request, $id)
    {
        $code = ActivationCode::with('test')->findOrFail($id);
        
        // Ambil semua kode dalam batch
        if ($request->batch) {
            $batchKey = $request->batch;
            
            if (isset($code->batch_id)) {
                $batchCodes = ActivationCode::where('batch_id', $code->batch_id)
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
        } else {
            $batchCodes = collect([$code]);
        }

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set properties
        $spreadsheet->getProperties()
            ->setCreator('Hyuka Admin')
            ->setTitle('Kode Aktivasi - ' . ($code->test->title ?? 'Export'))
            ->setSubject('Kode Aktivasi')
            ->setDescription('Export kode aktivasi untuk ' . ($code->test->title ?? 'N/A'));

        // Header utama
        $sheet->setCellValue('A1', 'DAFTAR KODE AKTIVASI');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Info Batch
        $sheet->setCellValue('A3', 'Modul:');
        $sheet->setCellValue('B3', $code->test->title ?? 'N/A');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        
        $sheet->setCellValue('A4', 'Tanggal Generate:');
        $sheet->setCellValue('B4', $code->created_at->format('d-m-Y H:i'));
        $sheet->getStyle('A4')->getFont()->setBold(true);
        
        $sheet->setCellValue('A5', 'Total Kode:');
        $sheet->setCellValue('B5', $batchCodes->count());
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        $sheet->setCellValue('A6', 'Kode Digunakan:');
        $sheet->setCellValue('B6', $batchCodes->where('status', 'Used')->count() . ' / ' . $batchCodes->count());
        $sheet->getStyle('A6')->getFont()->setBold(true);
        
        // Header Tabel
        $headerRow = 8;
        $headers = ['No', 'Kode Aktivasi', 'Status', 'Digunakan Oleh', 'Tanggal Digunakan', 'Nama Peserta'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        foreach ($headers as $index => $header) {
            $column = $columns[$index];
            $sheet->setCellValue($column . $headerRow, $header);
            $sheet->getStyle($column . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle($column . $headerRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($column . $headerRow)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($column . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column . $headerRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Data
        $row = $headerRow + 1;
        foreach ($batchCodes as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->code);
            $sheet->setCellValue('C' . $row, $item->status ?? 'Pending');
            $sheet->setCellValue('D' . $row, $item->user->email ?? '-');
            $sheet->setCellValue('E' . $row, $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d-m-Y H:i') : '-');
            $sheet->setCellValue('F' . $row, $item->user_name ?? ($item->user->name ?? '-'));
            
            // Center align untuk No dan Status
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Warna status
            if ($item->status == 'Used' || $item->status == 'Completed') {
                $sheet->getStyle('C' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('C6EFCE'); // Hijau muda
                $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('006100');
            } elseif ($item->status == 'Active' || $item->status == 'On Progress') {
                $sheet->getStyle('C' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFEB9C'); // Kuning muda
                $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('9C6500');
            } else {
                $sheet->getStyle('C' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFC7CE'); // Merah muda
                $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('9C0006');
            }
            
            $row++;
        }

        // Border untuk tabel
        $lastRow = $row - 1;
        $sheet->getStyle('A' . $headerRow . ':F' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000');
        
        // Border tebal untuk header
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        // Auto width
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set minimum width
        $sheet->getColumnDimension('B')->setWidth(20); // Kode Aktivasi
        $sheet->getColumnDimension('D')->setWidth(25); // Email
        $sheet->getColumnDimension('F')->setWidth(25); // Nama

        // Freeze panes (freeze header)
        $sheet->freezePane('A' . ($headerRow + 1));

        // Set header untuk download
        $filename = 'Kode_Aktivasi_' . ($code->batch_name ?? $code->test->title ?? 'Export') . '_' . now()->format('YmdHis') . '.xlsx';
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '.', $filename); // Clean filename
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}