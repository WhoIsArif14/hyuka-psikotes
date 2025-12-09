<?php

namespace App\Http\Controllers;

use App\Models\PauliTest;
use App\Models\PauliResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPauliController extends Controller
{
    public function start($id)
    {
        $test = PauliTest::with('alatTes')->findOrFail($id);
        
        // Generate test data
        $testData = $this->generateTestData($test);
        
        return view('user.pauli.test', compact('test', 'testData'));
    }

    public function submit(Request $request, $id)
    {
        $test = PauliTest::findOrFail($id);
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'test_data' => 'required|array',
            'completion_time' => 'required|integer',
        ]);

        // Calculate results
        $results = $this->calculateResults(
            $validated['test_data'],
            $validated['answers'],
            $test
        );

        // Save result
        $pauliResult = PauliResult::create([
            'user_id' => Auth::id(),
            'pauli_test_id' => $test->id,
            'test_data' => $validated['test_data'],
            'answers' => $validated['answers'],
            'column_performance' => $results['column_performance'],
            'total_answers' => $results['total_answers'],
            'correct_answers' => $results['correct_answers'],
            'accuracy' => $results['accuracy'],
            'average_speed' => $results['average_speed'],
            'completion_time' => $validated['completion_time'],
        ]);

        return redirect()->route('user.pauli.result', $pauliResult->id)
            ->with('success', 'Test selesai!');
    }

    public function result($resultId)
    {
        $result = PauliResult::with(['pauliTest', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($resultId);

        return view('user.pauli.result', compact('result'));
    }

    public function myResults()
    {
        $results = PauliResult::with('pauliTest')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.pauli.my-results', compact('results'));
    }

    private function generateTestData(PauliTest $test)
    {
        $data = [];
        
        for ($col = 0; $col < $test->total_columns; $col++) {
            $column = [];
            for ($pair = 0; $pair < $test->pairs_per_column; $pair++) {
                $column[] = [
                    'top' => rand(1, 9),
                    'bottom' => rand(1, 9),
                ];
            }
            $data[] = $column;
        }
        
        return $data;
    }

    private function calculateResults($testData, $answers, $test)
    {
        $totalAnswers = 0;
        $correctAnswers = 0;
        $columnPerformance = [];

        foreach ($testData as $colIndex => $column) {
            $colCorrect = 0;
            $colTotal = 0;

            foreach ($column as $pairIndex => $pair) {
                $correctSum = $pair['top'] + $pair['bottom'];
                $userAnswer = $answers[$colIndex][$pairIndex] ?? null;

                $colTotal++;
                $totalAnswers++;

                if ($userAnswer !== null) {
                    if ((int)$userAnswer === $correctSum) {
                        $colCorrect++;
                        $correctAnswers++;
                    }
                }
            }

            $columnPerformance[] = [
                'column' => $colIndex + 1,
                'total' => $colTotal,
                'correct' => $colCorrect,
                'accuracy' => $colTotal > 0 ? round(($colCorrect / $colTotal) * 100, 2) : 0,
            ];
        }

        $accuracy = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0;
        $averageSpeed = $totalAnswers > 0 ? round($totalAnswers / ($test->total_columns * $test->time_per_column / 60), 2) : 0;

        return [
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
            'accuracy' => $accuracy,
            'average_speed' => $averageSpeed,
            'column_performance' => $columnPerformance,
        ];
    }
}