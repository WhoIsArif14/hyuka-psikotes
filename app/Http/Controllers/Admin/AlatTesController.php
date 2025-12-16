<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\PapiQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlatTesController extends Controller
{
    /**
     * Helper method untuk mengecek apakah Alat Tes adalah PAPI Kostick
     */
    private function isPapiKostick($alatTes)
    {
        if (isset($alatTes->slug) && !empty($alatTes->slug)) {
            $slug = strtolower(trim($alatTes->slug));
            if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                return true;
            }
        }

        if (isset($alatTes->name) && !empty($alatTes->name)) {
            $name = strtolower(trim($alatTes->name));
            if (str_contains($name, 'papi') || str_contains($name, 'kostick') || str_contains($name, 'mami')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Menampilkan daftar semua Alat Tes (READ).
     */
    public function index(Request $request)
    {
        $AlatTes = AlatTes::latest()->get()->map(function ($alatTes) {
            if ($this->isPapiKostick($alatTes)) {
                $alatTes->questions_count = PapiQuestion::where('alat_tes_id', $alatTes->id)->count();
            } else {
                $alatTes->questions_count = $alatTes->questions()->count();
            }
            return $alatTes;
        });

        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $pagedData = $AlatTes->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $AlatTes = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $AlatTes->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.alat-tes.index', compact('AlatTes'));
    }

    /**
     * Menampilkan form untuk membuat Alat Tes baru.
     */
    public function create()
    {
        // ✅ Kirim array kosong untuk contoh soal
        $examples = [];
        return view('admin.alat-tes.create', compact('examples'));
    }

    /**
     * Menyimpan Alat Tes baru ke database. (CREATE)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name',
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            // ✅ Validasi untuk contoh soal
            'example_1_type' => 'nullable|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'example_1_question' => 'nullable|string|max:1000',
            'example_1_options' => 'nullable|string',
            'example_1_statement_a' => 'nullable|string|max:1000',
            'example_1_statement_b' => 'nullable|string|max:1000',
            'example_1_correct' => 'nullable|integer|min:0|max:10',
            'example_1_correct_multiple' => 'nullable|string',
            'example_1_memory_content' => 'nullable|string',
            'example_1_memory_type' => 'nullable|in:TEXT,IMAGE',
            'example_1_duration_seconds' => 'nullable|integer|min:1',
            'example_1_explanation' => 'nullable|string|max:500',

            'example_2_type' => 'nullable|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'example_2_question' => 'nullable|string|max:1000',
            'example_2_options' => 'nullable|string',
            'example_2_statement_a' => 'nullable|string|max:1000',
            'example_2_statement_b' => 'nullable|string|max:1000',
            'example_2_correct' => 'nullable|integer|min:0|max:10',
            'example_2_correct_multiple' => 'nullable|string',
            'example_2_memory_content' => 'nullable|string',
            'example_2_memory_type' => 'nullable|in:TEXT,IMAGE',
            'example_2_duration_seconds' => 'nullable|integer|min:1',
            'example_2_explanation' => 'nullable|string|max:500',
        ]);

        try {
            // 2. Persiapan Data
            $dataToCreate = [
                'name' => $validated['name'],
                'duration_minutes' => $validated['duration_minutes'],
                'instructions' => $validated['instructions'] ?? null,
                'slug' => Str::slug($validated['name']),
                'description' => null,
            ];

            // ✅ 3. Parse Example Questions
            $exampleQuestions = $this->parseExampleQuestions($request);
            $dataToCreate['example_questions'] = $exampleQuestions;

            // 4. Simpan Data ke Database
            $alatTes = AlatTes::create($dataToCreate);

            Log::info('Alat Tes created successfully', [
                'id' => $alatTes->id,
                'name' => $alatTes->name,
                'examples_count' => count($exampleQuestions)
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes baru berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Failed to create Alat Tes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan Alat Tes. Pesan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menampilkan form untuk mengedit Alat Tes.
     */
    public function edit(AlatTes $alat_te)
    {
        // ✅ Parse example questions untuk form edit
        $examples = [];

        if (!empty($alat_te->example_questions)) {
            if (is_string($alat_te->example_questions)) {
                $decoded = json_decode($alat_te->example_questions, true);
                $examples = $decoded ?? [];
            } else {
                $examples = $alat_te->example_questions;
            }
        }

        return view('admin.alat-tes.edit', [
            'AlatTes' => $alat_te,
            'examples' => $examples
        ]);
    }

    /**
     * Mengupdate Alat Tes di database. (UPDATE)
     */
    public function update(Request $request, AlatTes $alat_te)
    {
        // Validasi Input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:alat_tes,name,' . $alat_te->id,
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            // ✅ Validasi untuk contoh soal
            'example_1_type' => 'nullable|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'example_1_question' => 'nullable|string|max:1000',
            'example_1_options' => 'nullable|string',
            'example_1_statement_a' => 'nullable|string|max:1000',
            'example_1_statement_b' => 'nullable|string|max:1000',
            'example_1_correct' => 'nullable|integer|min:0|max:10',
            'example_1_correct_multiple' => 'nullable|string',
            'example_1_memory_content' => 'nullable|string',
            'example_1_memory_type' => 'nullable|in:TEXT,IMAGE',
            'example_1_duration_seconds' => 'nullable|integer|min:1',
            'example_1_explanation' => 'nullable|string|max:500',

            'example_2_type' => 'nullable|in:PILIHAN_GANDA,PILIHAN_GANDA_KOMPLEKS,HAFALAN,PAPIKOSTICK,PAULI,RMIB,BINARY,CUSTOM',
            'example_2_question' => 'nullable|string|max:1000',
            'example_2_options' => 'nullable|string',
            'example_2_statement_a' => 'nullable|string|max:1000',
            'example_2_statement_b' => 'nullable|string|max:1000',
            'example_2_correct' => 'nullable|integer|min:0|max:10',
            'example_2_correct_multiple' => 'nullable|string',
            'example_2_memory_content' => 'nullable|string',
            'example_2_memory_type' => 'nullable|in:TEXT,IMAGE',
            'example_2_duration_seconds' => 'nullable|integer|min:1',
            'example_2_explanation' => 'nullable|string|max:500',
        ]);

        try {
            // Persiapan Data
            $dataToUpdate = [
                'name' => $validated['name'],
                'duration_minutes' => $validated['duration_minutes'],
                'instructions' => $validated['instructions'] ?? null,
                'slug' => Str::slug($validated['name']),
            ];

            // ✅ Parse Example Questions
            $exampleQuestions = $this->parseExampleQuestions($request);
            $dataToUpdate['example_questions'] = $exampleQuestions;

            // Update Data
            $alat_te->update($dataToUpdate);

            Log::info('Alat Tes updated', [
                'id' => $alat_te->id,
                'name' => $alat_te->name,
                'examples_count' => count($exampleQuestions)
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('success', 'Alat Tes berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Failed to update Alat Tes', [
                'error' => $e->getMessage(),
                'alat_tes_id' => $alat_te->id
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal memperbarui: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menghapus Alat Tes dari database. (DELETE)
     */
    public function destroy(AlatTes $alat_te)
    {
        try {
            DB::beginTransaction();

            $name = $alat_te->name;
            $deletedQuestionsCount = 0;

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            try {
                if ($this->isPapiKostick($alat_te)) {
                    $deletedQuestionsCount = PapiQuestion::where('alat_tes_id', $alat_te->id)->count();
                    PapiQuestion::where('alat_tes_id', $alat_te->id)->delete();
                }

                $questionsCount = DB::table('questions')
                    ->where('alat_tes_id', $alat_te->id)
                    ->count();

                DB::table('questions')
                    ->where('alat_tes_id', $alat_te->id)
                    ->delete();

                $deletedQuestionsCount += $questionsCount;

                DB::table('question_options')->whereIn('question_id', function ($query) use ($alat_te) {
                    $query->select('id')
                        ->from('questions')
                        ->where('alat_tes_id', $alat_te->id);
                })->delete();

                $alat_te->delete();

                Log::info('Alat Tes deleted successfully', [
                    'id' => $alat_te->id,
                    'name' => $name,
                    'questions_deleted' => $deletedQuestionsCount
                ]);
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            DB::commit();

            $message = $deletedQuestionsCount > 0
                ? "Alat Tes '{$name}' dan {$deletedQuestionsCount} soal berhasil dihapus."
                : "Alat Tes '{$name}' berhasil dihapus.";

            return redirect()->route('admin.alat-tes.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::error('Failed to delete Alat Tes', [
                'alat_tes_id' => $alat_te->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.alat-tes.index')
                ->with('error', 'Gagal menghapus Alat Tes: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Helper method untuk parsing contoh soal dari form
     */
    private function parseExampleQuestions(Request $request)
    {
        $examples = [];

        // Helper to parse one example block
        $parseBlock = function ($prefix) use ($request) {
            $type = $request->input("{$prefix}_type") ?? 'PILIHAN_GANDA';
            $question = $request->input("{$prefix}_question") ?? '';

            // PAPI Kostick uses two statements (A and B)
            if ($type === 'PAPIKOSTICK') {
                $example = [
                    'type' => $type,
                    'question' => $question,
                    'statement_a' => $request->input("{$prefix}_statement_a") ?? '',
                    'statement_b' => $request->input("{$prefix}_statement_b") ?? '',
                    'explanation' => $request->input("{$prefix}_explanation") ?? '',
                ];
            } else {
                $options = array_filter(array_map('trim', explode("\n", $request->input("{$prefix}_options") ?? '')));

                $example = [
                    'type' => $type,
                    'question' => $question,
                    'options' => array_values($options),
                    'explanation' => $request->input("{$prefix}_explanation") ?? '',
                ];
            }

            if ($type === 'PILIHAN_GANDA_KOMPLEKS') {
                $raw = $request->input("{$prefix}_correct_multiple") ?? '';
                $answers = array_filter(array_map('trim', explode(',', $raw)), function ($v) {
                    return $v !== '';
                });
                $example['correct_answers'] = array_map('intval', $answers);
            } elseif ($type === 'HAFALAN') {
                $example['memory_content'] = $request->input("{$prefix}_memory_content") ?? '';
                $example['memory_type'] = $request->input("{$prefix}_memory_type") ?? 'TEXT';
                $example['duration_seconds'] = (int) ($request->input("{$prefix}_duration_seconds") ?? 10);
            } elseif ($type !== 'PAPIKOSTICK') {
                // Default single answer (only for non-PAPI types)
                $example['correct_answer'] = is_null($request->input("{$prefix}_correct")) ? null : (int) $request->input("{$prefix}_correct");
            }

            return $example;
        };

        // Contoh 1
        if ($request->filled('example_1_question') || $request->filled('example_1_memory_content')) {
            $examples[] = $parseBlock('example_1');
        }

        // Contoh 2
        if ($request->filled('example_2_question') || $request->filled('example_2_memory_content')) {
            $examples[] = $parseBlock('example_2');
        }

        return $examples;
    }
}
