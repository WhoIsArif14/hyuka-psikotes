<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalityTest;
use App\Models\PersonalityQuestion;
use App\Models\PersonalityResult;
use Illuminate\Support\Facades\Auth;

class PersonalityTestController extends Controller
{
    public function index()
    {
        $tests = PersonalityTest::orderBy('id')->get();
        return view('personality.index', compact('tests'));
    }

    public function show(PersonalityTest $personalityTest)
    {
        $questions = $personalityTest->questions()->get();
        return view('personality.test', compact('personalityTest', 'questions'));
    }

    public function submit(Request $request, PersonalityTest $personalityTest)
    {
        $questions = $personalityTest->questions()->get();
        $total = 0;
        $details = [];

        foreach ($questions as $q) {
            $key = 'q_' . $q->id;
            $val = (int) $request->input($key, 0);
            $total += $val;
            $details[$q->id] = $val;
        }

        $max = count($questions) * 5;
        $percent = $max ? round($total / $max * 100) : 0;

        // A simple interpretation example
        if ($percent >= 75) {
            $interpretation = 'Skor tinggi: cenderung ekstrovert / terencana / berani tergantung pertanyaan.';
        } elseif ($percent >= 50) {
            $interpretation = 'Skor sedang: kepribadian seimbang.';
        } else {
            $interpretation = 'Skor rendah: cenderung introvert / berhati-hati / tenang.';
        }

        $result = PersonalityResult::create([
            'personality_test_id' => $personalityTest->id,
            'user_id' => Auth::id(),
            'score' => $total,
            'interpretation' => $interpretation,
            'details' => $details,
        ]);

        return redirect()->route('personality.results.show', [$personalityTest->id, $result->id]);
    }

    public function showResult(PersonalityTest $personalityTest, PersonalityResult $result)
    {
        return view('personality.result', compact('personalityTest', 'result'));
    }
}
