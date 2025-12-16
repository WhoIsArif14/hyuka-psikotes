<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RmibQuestion;
use App\Models\AlatTes;
use Illuminate\Support\Facades\DB;

class RmibQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Cari Alat Tes RMIB otomatis
        $rmibAlatTes = AlatTes::where('slug', 'like', '%rmib%')
            ->orWhere('name', 'like', '%RMIB%')
            ->orWhere('name', 'like', '%Minat%')
            ->first();

        if (!$rmibAlatTes) {
            $this->command->error('❌ Alat Tes RMIB tidak ditemukan!');
            $this->command->info('💡 Buat Alat Tes RMIB terlebih dahulu di Admin Panel.');
            return;
        }

        $this->command->info("✅ Found RMIB Alat Tes: {$rmibAlatTes->name} (ID: {$rmibAlatTes->id})");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Hapus hanya untuk alat tes ini
        RmibQuestion::where('alat_tes_id', $rmibAlatTes->id)->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tables = $this->getRmibTables();
        $createdCount = 0;

        foreach ($tables as $table) {
            RmibQuestion::create(array_merge(
                ['alat_tes_id' => $rmibAlatTes->id], 
                $table
            ));
            $createdCount++;
        }

        $this->command->info("✅ {$createdCount} RMIB tables seeded successfully!");
        $this->command->info("📋 Alat Tes: {$rmibAlatTes->name}");
    }

    private function getRmibTables()
    {
        return [
            // TABEL 1
            [
                'item_number' => 1,
                'group_title' => 'Tabel 1',
                'statement_a' => 'Koreografer',
                'statement_b' => 'Teller',
                'statement_c' => 'Auditor',
                'statement_d' => 'Penulis Buku',
                'statement_e' => 'Operator Mesin',
                'statement_f' => 'Ahli Gizi',
                'statement_g' => 'Penghantar Musik',
                'statement_h' => 'Fasilitator Outbound',
                'statement_i' => 'Customer Care',
                'statement_j' => 'Administrator Kantor',
                'statement_k' => 'Tukang Kayu',
                'statement_l' => 'Perawat',
                'key_a' => 'A',
                'key_b' => 'C',
                'key_c' => 'C',
                'key_d' => 'L',
                'key_e' => 'M',
                'key_f' => 'Me',
                'key_g' => 'Mu',
                'key_h' => 'O',
                'key_i' => 'P',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 2
            [
                'item_number' => 2,
                'group_title' => 'Tabel 2',
                'statement_a' => 'Administrator',
                'statement_b' => 'Data Scientist',
                'statement_c' => 'Reporter',
                'statement_d' => 'Product Design Engineer',
                'statement_e' => 'Paramedis',
                'statement_f' => 'Dirigen / Konduktor',
                'statement_g' => 'Traveler',
                'statement_h' => 'Influencer',
                'statement_i' => 'Hair Stylist',
                'statement_j' => 'Sekretaris',
                'statement_k' => 'Teknisi Listrik',
                'statement_l' => 'Dokter',
                'key_a' => 'P',
                'key_b' => 'C',
                'key_c' => 'L',
                'key_d' => 'S',
                'key_e' => 'Me',
                'key_f' => 'Mu',
                'key_g' => 'O',
                'key_h' => 'SS',
                'key_i' => 'A',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 3
            [
                'item_number' => 3,
                'group_title' => 'Tabel 3',
                'statement_a' => 'Manajer Investasi',
                'statement_b' => 'Pengarang Cerita',
                'statement_c' => 'Mekanik',
                'statement_d' => 'Apoteker',
                'statement_e' => 'Music Programmer',
                'statement_f' => 'Ahli Konstruksi',
                'statement_g' => 'Public Relations',
                'statement_h' => 'Hand Crafter',
                'statement_i' => 'Penulis',
                'statement_j' => 'Entry Data',
                'statement_k' => 'Tukang Pipa',
                'statement_l' => 'Fisioterapis',
                'key_a' => 'C',
                'key_b' => 'L',
                'key_c' => 'M',
                'key_d' => 'Me',
                'key_e' => 'Mu',
                'key_f' => 'Pr',
                'key_g' => 'P',
                'key_h' => 'A',
                'key_i' => 'L',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 4
            [
                'item_number' => 4,
                'group_title' => 'Tabel 4',
                'statement_a' => 'Jurnalis',
                'statement_b' => 'Machine Learning Engineer',
                'statement_c' => 'Radiolog',
                'statement_d' => 'Produser Musik',
                'statement_e' => 'Pramugari',
                'statement_f' => 'Pengusaha Online Shop',
                'statement_g' => 'Sinematografer',
                'statement_h' => 'Ahli Geologi',
                'statement_i' => 'Tenaga Pengajar',
                'statement_j' => 'Administrasi',
                'statement_k' => 'Maintenance Bangunan',
                'statement_l' => 'Ahli Laboratorium Medis',
                'key_a' => 'L',
                'key_b' => 'S',
                'key_c' => 'Me',
                'key_d' => 'Mu',
                'key_e' => 'P',
                'key_f' => 'C',
                'key_g' => 'A',
                'key_h' => 'S',
                'key_i' => 'SS',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 5
            [
                'item_number' => 5,
                'group_title' => 'Tabel 5',
                'statement_a' => 'Automotive Engineer',
                'statement_b' => 'Dokter Spesialis',
                'statement_c' => 'Pemain Alat Musik',
                'statement_d' => 'Kontraktor',
                'statement_e' => 'Interviewer',
                'statement_f' => 'Content Creator',
                'statement_g' => 'Ahli Astronomi',
                'statement_h' => 'Pekerja Sosial',
                'statement_i' => 'Animator',
                'statement_j' => 'Filing Clerk',
                'statement_k' => 'Renovator',
                'statement_l' => 'Ahli Rehabilitasi',
                'key_a' => 'M',
                'key_b' => 'Me',
                'key_c' => 'Mu',
                'key_d' => 'Pr',
                'key_e' => 'P',
                'key_f' => 'A',
                'key_g' => 'S',
                'key_h' => 'SS',
                'key_i' => 'A',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 6
            [
                'item_number' => 6,
                'group_title' => 'Tabel 6',
                'statement_a' => 'Dokter Umum',
                'statement_b' => 'Komposer',
                'statement_c' => 'Surveyor',
                'statement_d' => 'Pengacara',
                'statement_e' => 'Food Vlogger',
                'statement_f' => 'Ahli Botani',
                'statement_g' => 'Tim SAR',
                'statement_h' => 'Fashion Designer',
                'statement_i' => 'Sekretaris Eksekutif',
                'statement_j' => 'Arsiparis',
                'statement_k' => 'Teknisi AC',
                'statement_l' => 'Farmasis',
                'key_a' => 'Me',
                'key_b' => 'Mu',
                'key_c' => 'O',
                'key_d' => 'P',
                'key_e' => 'P',
                'key_f' => 'S',
                'key_g' => 'O',
                'key_h' => 'A',
                'key_i' => 'Cl',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 7
            [
                'item_number' => 7,
                'group_title' => 'Tabel 7',
                'statement_a' => 'Penata Musik',
                'statement_b' => 'Fotografer',
                'statement_c' => 'Trainer',
                'statement_d' => 'Komikus',
                'statement_e' => 'Ahli Forensik',
                'statement_f' => 'Pemadam Kebakaran',
                'statement_g' => 'Art Director',
                'statement_h' => 'Personalia',
                'statement_i' => 'Marketing Analyst',
                'statement_j' => 'Scheduler',
                'statement_k' => 'Teknisi Elektronik',
                'statement_l' => 'Analis Kesehatan',
                'key_a' => 'Mu',
                'key_b' => 'A',
                'key_c' => 'SS',
                'key_d' => 'A',
                'key_e' => 'S',
                'key_f' => 'O',
                'key_g' => 'A',
                'key_h' => 'P',
                'key_i' => 'C',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 8
            [
                'item_number' => 8,
                'group_title' => 'Tabel 8',
                'statement_a' => 'Tour Guide',
                'statement_b' => 'Konsultan',
                'statement_c' => 'Translator',
                'statement_d' => 'Ahli Biologi',
                'statement_e' => 'Perawat / Caregiver',
                'statement_f' => 'Ilustrator',
                'statement_g' => 'Aktuaris',
                'statement_h' => 'Statistician',
                'statement_i' => 'Advertising Copywriter',
                'statement_j' => 'Administrasi Umum',
                'statement_k' => 'Welder',
                'statement_l' => 'Ahli Terapi',
                'key_a' => 'O',
                'key_b' => 'P',
                'key_c' => 'L',
                'key_d' => 'S',
                'key_e' => 'Me',
                'key_f' => 'A',
                'key_g' => 'C',
                'key_h' => 'C',
                'key_i' => 'L',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 9
            [
                'item_number' => 9,
                'group_title' => 'Tabel 9',
                'statement_a' => 'Sales',
                'statement_b' => 'Pengrajin / Crafter',
                'statement_c' => 'Ahli Meteorologi',
                'statement_d' => 'Konselor',
                'statement_e' => 'Desainer Grafis',
                'statement_f' => 'Customer Service',
                'statement_g' => 'Financial Planner',
                'statement_h' => 'Penulis Karya Ilmiah',
                'statement_i' => 'Robotic Engineer',
                'statement_j' => 'Data Entry',
                'statement_k' => 'Mekanik Otomotif',
                'statement_l' => 'Radiografer',
                'key_a' => 'P',
                'key_b' => 'Pr',
                'key_c' => 'S',
                'key_d' => 'SS',
                'key_e' => 'A',
                'key_f' => 'P',
                'key_g' => 'C',
                'key_h' => 'L',
                'key_i' => 'M',
                'key_j' => 'Cl',
                'key_k' => 'M',
                'key_l' => 'Me',
            ],

            // TABEL 10
            [
                'item_number' => 10,
                'group_title' => 'Tabel 10',
                'statement_a' => 'Patissier',
                'statement_b' => 'Konservasionis',
                'statement_c' => 'Psikolog',
                'statement_d' => 'Desainer Interior',
                'statement_e' => 'Kasir',
                'statement_f' => 'Ahli Matematika',
                'statement_g' => 'Technical Writer',
                'statement_h' => 'Research Engineer',
                'statement_i' => 'Psikiater',
                'statement_j' => 'Office Manager',
                'statement_k' => 'Installer',
                'statement_l' => 'Ahli Kesehatan Masyarakat',
                'key_a' => 'Pr',
                'key_b' => 'O',
                'key_c' => 'SS',
                'key_d' => 'A',
                'key_e' => 'Cl',
                'key_f' => 'C',
                'key_g' => 'L',
                'key_h' => 'S',
                'key_i' => 'Me',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 11
            [
                'item_number' => 11,
                'group_title' => 'Tabel 11',
                'statement_a' => 'Ilmuwan',
                'statement_b' => 'Relawan',
                'statement_c' => 'Arsitek',
                'statement_d' => 'Resepsionis',
                'statement_e' => 'Staf Finance',
                'statement_f' => 'Penulis Dongeng',
                'statement_g' => 'Desainer Mekanik',
                'statement_h' => 'Fisioterapis',
                'statement_i' => 'Penyanyi',
                'statement_j' => 'Inventory Manager',
                'statement_k' => 'Konstruksi Worker',
                'statement_l' => 'Ahli Bedah',
                'key_a' => 'S',
                'key_b' => 'SS',
                'key_c' => 'A',
                'key_d' => 'P',
                'key_e' => 'C',
                'key_f' => 'L',
                'key_g' => 'M',
                'key_h' => 'Me',
                'key_i' => 'Mu',
                'key_j' => 'Cl',
                'key_k' => 'Pr',
                'key_l' => 'Me',
            ],

            // TABEL 12
            [
                'item_number' => 12,
                'group_title' => 'Tabel 12',
                'statement_a' => 'Volunteer',
                'statement_b' => 'Penata Artistik',
                'statement_c' => 'Entry Data Operator',
                'statement_d' => 'Programmer',
                'statement_e' => 'Content Writer',
                'statement_f' => 'Aerospace Engineer',
                'statement_g' => 'Ahli Lab Klinis',
                'statement_h' => 'Penulis Lagu',
                'statement_i' => 'Arkeolog',
                'statement_j' => 'Back Office Staff',
                'statement_k' => 'Teknisi Komputer',
                'statement_l' => 'Ahli Diagnostik',
                'key_a' => 'SS',
                'key_b' => 'A',
                'key_c' => 'Cl',
                'key_d' => 'S',
                'key_e' => 'L',
                'key_f' => 'M',
                'key_g' => 'Me',
                'key_h' => 'Mu',
                'key_i' => 'S',
                'key_j' => 'Cl',
                'key_k' => 'M',
                'key_l' => 'Me',
            ],
        ];
    }
}