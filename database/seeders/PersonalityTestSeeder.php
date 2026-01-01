<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalityTest;
use App\Models\PersonalityQuestion;

class PersonalityTestSeeder extends Seeder
{
    public function run(): void
    {
        $test = PersonalityTest::create([
            'title' => 'Tes Kepribadian Singkat (Contoh)',
            'description' => 'Tes singkat 5 pertanyaan untuk contoh: skala 1-5 (Sangat Tidak Setuju -> Sangat Setuju)'
        ]);

        $options = [
            ['label' => 'Sangat Tidak Setuju', 'value' => 1],
            ['label' => 'Tidak Setuju', 'value' => 2],
            ['label' => 'Netral', 'value' => 3],
            ['label' => 'Setuju', 'value' => 4],
            ['label' => 'Sangat Setuju', 'value' => 5],
        ];

        $questions = [
            'Saya suka bergaul dan bertemu banyak orang.',
            'Saya biasanya merencanakan segala sesuatu dengan teliti.',
            'Saya mudah merasa cemas dalam situasi baru.',
            'Saya suka mengambil resiko bila perlu.',
            'Saya memperhatikan detail dan rapi bekerja.'
        ];

        foreach ($questions as $index => $q) {
            PersonalityQuestion::create([
                'personality_test_id' => $test->id,
                'question' => $q,
                'options' => $options,
                'order' => $index + 1,
            ]);
        }
    }
}
