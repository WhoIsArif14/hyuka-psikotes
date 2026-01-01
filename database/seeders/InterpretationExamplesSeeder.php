<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\InterpretationRule;

class InterpretationExamplesSeeder extends Seeder
{
    public function run(): void
    {
        // Create example rules for Personality tests (assuming module Test already exists)
        $tests = Test::where('title', 'like', '%Kepribadian%')->get();
        foreach ($tests as $t) {
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 0,
                'max_score' => 49,
                'interpretation_text' => 'Skor rendah: cenderung introvert / berhati-hati',
            ]);
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 50,
                'max_score' => 74,
                'interpretation_text' => 'Skor sedang: kepribadian seimbang',
            ]);
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 75,
                'max_score' => 100,
                'interpretation_text' => 'Skor tinggi: cenderung ekstrovert / aktif',
            ]);
        }

        // RMIB: create textual examples attached to a Test named 'RMIB'
        $rmibTests = Test::where('title', 'like', '%RMIB%')->get();
        foreach ($rmibTests as $t) {
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 0,
                'max_score' => 30,
                'interpretation_text' => 'Minat rendah untuk kelompok ini',
            ]);
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 31,
                'max_score' => 60,
                'interpretation_text' => 'Minat sedang',
            ]);
            InterpretationRule::create([
                'test_id' => $t->id,
                'min_score' => 61,
                'max_score' => 100,
                'interpretation_text' => 'Minat tinggi',
            ]);
        }

        $this->command->info('✅ Interpretation examples seeded (if matching Tests exist).');
    }
}
