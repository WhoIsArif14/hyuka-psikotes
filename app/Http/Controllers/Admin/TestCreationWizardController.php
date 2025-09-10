<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestCreationWizardController extends Controller
{
    /**
     * Langkah 1: Menampilkan halaman untuk memilih kategori.
     */
    public function step1_category()
    {
        Session::forget('wizard_data');
        $categories = TestCategory::all();
        return view('admin.wizard.step1-category', compact('categories'));
    }

    /**
     * Menyimpan pilihan kategori ke session dan lanjut ke langkah 2.
     */
    public function postStep1_category(Request $request)
    {
        $validated = $request->validate(['category_id' => 'required|exists:test_categories,id']);
        Session::put('wizard_data.category_id', $validated['category_id']);
        
        // PERBAIKAN DI SINI: Menggunakan nama route yang benar
        return redirect()->route('admin.wizard.step2');
    }

    /**
     * Langkah 2: Menampilkan template & jenjang berdasarkan kategori.
     */
    public function step2_template()
    {
        $categoryId = Session::get('wizard_data.category_id');
        if (!$categoryId) {
            return redirect()->route('admin.wizard.step1')->with('error', 'Silakan pilih kategori terlebih dahulu.');
        }

        $templates = Test::where('is_template', true)
                         ->where('test_category_id', $categoryId)
                         ->withCount('questions')
                         ->get();
        $jenjangs = Jenjang::all();
        
        return view('admin.wizard.step2-template', compact('templates', 'jenjangs'));
    }

    /**
     * Membuat tes baru dari template dan lanjut ke langkah 3.
     */
    public function postStep2_template(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:tests,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'new_test_title' => 'required|string|max:255',
        ]);

        $template = Test::with(['questions.options'])->find($validated['template_id']);
        $newTest = null;

        DB::transaction(function () use ($template, $validated, &$newTest) {
            // 1. Duplikasi data tes utama
            $newTest = $template->replicate(['test_code']); // Replicate, tapi jangan copy test_code
            $newTest->title = $validated['new_test_title'];
            $newTest->jenjang_id = $validated['jenjang_id'];
            $newTest->is_template = false;
            $newTest->is_published = false;
            $newTest->available_from = null;
            $newTest->available_to = null;
            $newTest->save(); // save() akan memicu boot method untuk generate kode baru

            // 2. Duplikasi setiap soal dan pilihan jawabannya
            foreach ($template->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->test_id = $newTest->id;
                $newQuestion->save();

                foreach ($question->options as $option) {
                    $newOption = $option->replicate();
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                }
            }
        });

        if ($newTest) {
            // PERBAIKAN DI SINI: Menggunakan nama route yang benar
            return redirect()->route('admin.wizard.step3', $newTest);
        }

        return redirect()->back()->with('error', 'Gagal membuat tes. Silakan coba lagi.');
    }

    /**
     * Langkah 3: Menampilkan halaman untuk mengatur jadwal.
     */
    public function step3_schedule(Test $test)
    {
        return view('admin.wizard.step3-schedule', compact('test'));
    }

    /**
     * Menyimpan jadwal dan menyelesaikan proses.
     */
    public function postStep3_schedule(Request $request, Test $test)
    {
        $validated = $request->validate([
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'is_published' => 'nullable|boolean',
        ]);

        $test->update([
            'available_from' => $validated['available_from'] ?? null,
            'available_to' => $validated['available_to'] ?? null,
            'is_published' => $request->has('is_published'),
        ]);

        Session::forget('wizard_data');

        return redirect()->route('admin.tests.index')->with('success', 'Sesi tes baru berhasil dibuat!');
    }
}
