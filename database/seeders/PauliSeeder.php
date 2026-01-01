<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AlatTes;
use App\Models\PauliTest;

class PauliSeeder extends Seeder
{
    public function run(): void
    {
        // Check if an Alat Tes with "pauli" exists
        $alat = AlatTes::where('slug', 'like', '%pauli%')
            ->orWhere('name', 'like', '%Pauli%')
            ->first();

        if (!$alat) {
            $alat = AlatTes::create([
                'name' => 'Pauli Test',
                'slug' => 'pauli',
                'duration_minutes' => 10,
                'description' => 'Pauli Test (Numerical) measures speed, accuracy, and consistency. Auto-generated numeric sheets per configuration.',
                'instructions' => 'Pada setiap kolom terdapat pasangan angka. Tandai yang sesuai dalam batas waktu per kolom.',
                'example_questions' => json_encode([
                    ['type' => 'PAULI', 'title' => 'Contoh Pauli', 'content' => 'Contoh cara mengerjakan Pauli Test'],
                ]),
            ]);

            $this->command->info("✅ Alat Tes 'Pauli Test' dibuat (ID: {$alat->id}).");
        } else {
            $this->command->info("ℹ️ Alat Tes 'Pauli Test' sudah ada (ID: {$alat->id}).");
        }

        // Create default PauliTest configuration if missing
        $pauli = PauliTest::where('alat_tes_id', $alat->id)->first();
        if (!$pauli) {
            PauliTest::create([
                'alat_tes_id' => $alat->id,
                'total_columns' => 45,
                'pairs_per_column' => 45,
                'time_per_column' => 60, // seconds
            ]);
            $this->command->info('✅ Konfigurasi PauliTest default dibuat (45 columns, 45 pairs/column, 60s per column).');
        } else {
            $this->command->info('ℹ️ Konfigurasi PauliTest sudah ada.');
        }
    }
}
