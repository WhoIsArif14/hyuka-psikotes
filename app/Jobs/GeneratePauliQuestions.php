<?php

namespace App\Jobs;

use App\Models\PauliTest;
use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePauliQuestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pauliTestId;

    /**
     * Create a new job instance.
     */
    public function __construct($pauliTestId)
    {
        $this->pauliTestId = $pauliTestId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $pauliTest = PauliTest::find($this->pauliTestId);
        if (!$pauliTest) {
            Log::warning('GeneratePauliQuestions: PauliTest not found', ['id' => $this->pauliTestId]);
            return;
        }

        $created = 0;
        for ($col = 0; $col < $pauliTest->total_columns; $col++) {
            for ($pair = 0; $pair < $pauliTest->pairs_per_column; $pair++) {
                $top = rand(1, 9);
                $bottom = rand(1, 9);

                Question::create([
                    'alat_tes_id' => $pauliTest->alat_tes_id,
                    'pauli_test_id' => $pauliTest->id,
                    'type' => 'PAULI',
                    'question_text' => null,
                    'example_question' => null,
                    'instructions' => null,
                    'options' => null,
                    'correct_answer_index' => null,
                    'correct_answers' => null,
                    'ranking_category' => null,
                    'ranking_weight' => null,
                    'metadata' => json_encode([
                        'column' => $col + 1,
                        'pair_index' => $pair + 1,
                        'top' => $top,
                        'bottom' => $bottom,
                        'correct_sum' => $top + $bottom,
                    ]),
                ]);

                $created++;
            }
        }

        Log::info('GeneratePauliQuestions job finished', ['pauli_test_id' => $pauliTest->id, 'created' => $created]);
    }
}
