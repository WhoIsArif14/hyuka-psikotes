<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RmibItem;
use Illuminate\Support\Facades\DB;

class RmibItemSeeder extends Seeder
{
    public function run(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        RmibItem::truncate();
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $items = $this->getRmibStandardItems();

        foreach ($items as $item) {
            RmibItem::create($item);
        }

        $this->command->info('✅ 144 RMIB items seeded successfully!');
        $this->command->info('📊 Breakdown: 12 groups (A-L), each with 12 items');
    }

    private function getRmibStandardItems()
    {
        return [
            // ========== GROUP A - OUTDOOR ==========
            ['item_number' => 1, 'group_label' => 'A', 'position_in_group' => 1, 'description' => 'Koreografer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 2, 'group_label' => 'A', 'position_in_group' => 2, 'description' => 'Administrator', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 3, 'group_label' => 'A', 'position_in_group' => 3, 'description' => 'Manajer Investasi', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 4, 'group_label' => 'A', 'position_in_group' => 4, 'description' => 'Jurnalis', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 5, 'group_label' => 'A', 'position_in_group' => 5, 'description' => 'Automative Engineer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 6, 'group_label' => 'A', 'position_in_group' => 6, 'description' => 'Dokter Umum', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 7, 'group_label' => 'A', 'position_in_group' => 7, 'description' => 'Penata Musik / Music Arranger', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 8, 'group_label' => 'A', 'position_in_group' => 8, 'description' => 'Tour Guide', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 9, 'group_label' => 'A', 'position_in_group' => 9, 'description' => 'Sales', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 10, 'group_label' => 'A', 'position_in_group' => 10, 'description' => 'Patissier', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 11, 'group_label' => 'A', 'position_in_group' => 11, 'description' => 'Ilmuwan', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 12, 'group_label' => 'A', 'position_in_group' => 12, 'description' => 'Volunteer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],

            // ========== GROUP B - MECHANICAL ==========
            ['item_number' => 13, 'group_label' => 'B', 'position_in_group' => 1, 'description' => 'Teller', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 14, 'group_label' => 'B', 'position_in_group' => 2, 'description' => 'Data Scientist', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 15, 'group_label' => 'B', 'position_in_group' => 3, 'description' => 'Pengarang Cerita', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 16, 'group_label' => 'B', 'position_in_group' => 4, 'description' => 'Machine Learning Engineer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 17, 'group_label' => 'B', 'position_in_group' => 5, 'description' => 'Dokter Spesialis', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 18, 'group_label' => 'B', 'position_in_group' => 6, 'description' => 'Komposer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 19, 'group_label' => 'B', 'position_in_group' => 7, 'description' => 'Fotografer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 20, 'group_label' => 'B', 'position_in_group' => 8, 'description' => 'Konsultan', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 21, 'group_label' => 'B', 'position_in_group' => 9, 'description' => 'Pengrajin / Crafter', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 22, 'group_label' => 'B', 'position_in_group' => 10, 'description' => 'Konservasionis', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 23, 'group_label' => 'B', 'position_in_group' => 11, 'description' => 'Relawan', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 24, 'group_label' => 'B', 'position_in_group' => 12, 'description' => 'Penata artistik', 'interest_area' => 'MECHANICAL', 'version' => '1995'],

            // ========== GROUP C - COMPUTATIONAL ==========
            ['item_number' => 25, 'group_label' => 'C', 'position_in_group' => 1, 'description' => 'Auditor', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 26, 'group_label' => 'C', 'position_in_group' => 2, 'description' => 'Reporter', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 27, 'group_label' => 'C', 'position_in_group' => 3, 'description' => 'Mekanik', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 28, 'group_label' => 'C', 'position_in_group' => 4, 'description' => 'Radiolog', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 29, 'group_label' => 'C', 'position_in_group' => 5, 'description' => 'Pemain Alat Musik / Musisi', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 30, 'group_label' => 'C', 'position_in_group' => 6, 'description' => 'Surveyor', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 31, 'group_label' => 'C', 'position_in_group' => 7, 'description' => 'Trainer', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 32, 'group_label' => 'C', 'position_in_group' => 8, 'description' => 'Translator', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 33, 'group_label' => 'C', 'position_in_group' => 9, 'description' => 'Ahli Meteorologi', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 34, 'group_label' => 'C', 'position_in_group' => 10, 'description' => 'Psikolog', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 35, 'group_label' => 'C', 'position_in_group' => 11, 'description' => 'Arsitek', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 36, 'group_label' => 'C', 'position_in_group' => 12, 'description' => 'Entry Data', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],

            // ========== GROUP D - SCIENTIFIC ==========
            ['item_number' => 37, 'group_label' => 'D', 'position_in_group' => 1, 'description' => 'Penulis Buku', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 38, 'group_label' => 'D', 'position_in_group' => 2, 'description' => 'Product Design Engineer', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 39, 'group_label' => 'D', 'position_in_group' => 3, 'description' => 'Apoteker', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 40, 'group_label' => 'D', 'position_in_group' => 4, 'description' => 'Produser Rekaman/Musik', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 41, 'group_label' => 'D', 'position_in_group' => 5, 'description' => 'Kontraktor', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 42, 'group_label' => 'D', 'position_in_group' => 6, 'description' => 'Pengacara', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 43, 'group_label' => 'D', 'position_in_group' => 7, 'description' => 'Komikus', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 44, 'group_label' => 'D', 'position_in_group' => 8, 'description' => 'Ahli Biologi', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 45, 'group_label' => 'D', 'position_in_group' => 9, 'description' => 'Konselor', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 46, 'group_label' => 'D', 'position_in_group' => 10, 'description' => 'Desainer Interior', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 47, 'group_label' => 'D', 'position_in_group' => 11, 'description' => 'Resepsionis / Front Desk', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 48, 'group_label' => 'D', 'position_in_group' => 12, 'description' => 'Programmer', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],

            // ========== GROUP E - PERSONAL_CONTACT ==========
            ['item_number' => 49, 'group_label' => 'E', 'position_in_group' => 1, 'description' => 'Operator Mesin', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 50, 'group_label' => 'E', 'position_in_group' => 2, 'description' => 'Paramedis', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 51, 'group_label' => 'E', 'position_in_group' => 3, 'description' => 'Music Programmer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 52, 'group_label' => 'E', 'position_in_group' => 4, 'description' => 'Pramugari', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 53, 'group_label' => 'E', 'position_in_group' => 5, 'description' => 'Interviewer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 54, 'group_label' => 'E', 'position_in_group' => 6, 'description' => 'Food Vlogger', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 55, 'group_label' => 'E', 'position_in_group' => 7, 'description' => 'Ahli Forensik', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 56, 'group_label' => 'E', 'position_in_group' => 8, 'description' => 'Perawat / Caregiver', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 57, 'group_label' => 'E', 'position_in_group' => 9, 'description' => 'Desainer Grafis', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 58, 'group_label' => 'E', 'position_in_group' => 10, 'description' => 'Kasir', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 59, 'group_label' => 'E', 'position_in_group' => 11, 'description' => 'Staf Finance', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 60, 'group_label' => 'E', 'position_in_group' => 12, 'description' => 'Content Writer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],

            // ========== GROUP F - AESTHETIC ==========
            ['item_number' => 61, 'group_label' => 'F', 'position_in_group' => 1, 'description' => 'Ahli Gizi', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 62, 'group_label' => 'F', 'position_in_group' => 2, 'description' => 'Dirigen / Konduktor', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 63, 'group_label' => 'F', 'position_in_group' => 3, 'description' => 'Ahli Konstruksi', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 64, 'group_label' => 'F', 'position_in_group' => 4, 'description' => 'Pengusaha Online Shop', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 65, 'group_label' => 'F', 'position_in_group' => 5, 'description' => 'Content Creator', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 66, 'group_label' => 'F', 'position_in_group' => 6, 'description' => 'Ahli Botani', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 67, 'group_label' => 'F', 'position_in_group' => 7, 'description' => 'Pemadam Kebakaran', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 68, 'group_label' => 'F', 'position_in_group' => 8, 'description' => 'Ilustrator', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 69, 'group_label' => 'F', 'position_in_group' => 9, 'description' => 'Customer Service', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 70, 'group_label' => 'F', 'position_in_group' => 10, 'description' => 'Ahli Matematika', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 71, 'group_label' => 'F', 'position_in_group' => 11, 'description' => 'Penulis Dongeng', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 72, 'group_label' => 'F', 'position_in_group' => 12, 'description' => 'Aerospace Engineer', 'interest_area' => 'AESTHETIC', 'version' => '1995'],

            // ========== GROUP G - LITERARY ==========
            ['item_number' => 73, 'group_label' => 'G', 'position_in_group' => 1, 'description' => 'Pengamat Musik', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 74, 'group_label' => 'G', 'position_in_group' => 2, 'description' => 'Traveler', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 75, 'group_label' => 'G', 'position_in_group' => 3, 'description' => 'Public Relation', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 76, 'group_label' => 'G', 'position_in_group' => 4, 'description' => 'Sinematographer', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 77, 'group_label' => 'G', 'position_in_group' => 5, 'description' => 'Ahli Astronomi', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 78, 'group_label' => 'G', 'position_in_group' => 6, 'description' => 'Tim SAR', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 79, 'group_label' => 'G', 'position_in_group' => 7, 'description' => 'Art Director', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 80, 'group_label' => 'G', 'position_in_group' => 8, 'description' => 'Aktuntan', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 81, 'group_label' => 'G', 'position_in_group' => 9, 'description' => 'Financial Planner', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 82, 'group_label' => 'G', 'position_in_group' => 10, 'description' => 'Technical Writer', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 83, 'group_label' => 'G', 'position_in_group' => 11, 'description' => 'Desainer Mekanik', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 84, 'group_label' => 'G', 'position_in_group' => 12, 'description' => 'Ahli Laboratorium Klinis', 'interest_area' => 'LITERARY', 'version' => '1995'],

            // ========== GROUP H - MUSICAL ==========
            ['item_number' => 85, 'group_label' => 'H', 'position_in_group' => 1, 'description' => 'Fasilitator Outbound', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 86, 'group_label' => 'H', 'position_in_group' => 2, 'description' => 'Influencer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 87, 'group_label' => 'H', 'position_in_group' => 3, 'description' => 'Hand Crafter', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 88, 'group_label' => 'H', 'position_in_group' => 4, 'description' => 'Ahli Geologi', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 89, 'group_label' => 'H', 'position_in_group' => 5, 'description' => 'Pekerja Sosial', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 90, 'group_label' => 'H', 'position_in_group' => 6, 'description' => 'Fashion Designer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 91, 'group_label' => 'H', 'position_in_group' => 7, 'description' => 'Personalia', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 92, 'group_label' => 'H', 'position_in_group' => 8, 'description' => 'Statistician', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 93, 'group_label' => 'H', 'position_in_group' => 9, 'description' => 'Penulis Karya Ilmiah', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 94, 'group_label' => 'H', 'position_in_group' => 10, 'description' => 'Research and Development Engineer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 95, 'group_label' => 'H', 'position_in_group' => 11, 'description' => 'Fisioterapis', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 96, 'group_label' => 'H', 'position_in_group' => 12, 'description' => 'Penulis Lagu', 'interest_area' => 'MUSICAL', 'version' => '1995'],

            // ========== GROUP I - SOCIAL_SERVICE ==========
            ['item_number' => 97, 'group_label' => 'I', 'position_in_group' => 1, 'description' => 'Customer Care', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 98, 'group_label' => 'I', 'position_in_group' => 2, 'description' => 'Hair Stylist', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 99, 'group_label' => 'I', 'position_in_group' => 3, 'description' => 'Penulis', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 100, 'group_label' => 'I', 'position_in_group' => 4, 'description' => 'Tenaga Pengajar', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 101, 'group_label' => 'I', 'position_in_group' => 5, 'description' => 'Animator', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 102, 'group_label' => 'I', 'position_in_group' => 6, 'description' => 'Sekretaris', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 103, 'group_label' => 'I', 'position_in_group' => 7, 'description' => 'Marketing Analyst', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 104, 'group_label' => 'I', 'position_in_group' => 8, 'description' => 'Advertising Copywriter', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 105, 'group_label' => 'I', 'position_in_group' => 9, 'description' => 'Robotic Engineer', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 106, 'group_label' => 'I', 'position_in_group' => 10, 'description' => 'Psikiater', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 107, 'group_label' => 'I', 'position_in_group' => 11, 'description' => 'Penyanyi', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 108, 'group_label' => 'I', 'position_in_group' => 12, 'description' => 'Arkeolog', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],

            // ========== GROUP J - CLERICAL ==========
            ['item_number' => 109, 'group_label' => 'J', 'position_in_group' => 1, 'description' => 'Mengelola administrasi kantor', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 110, 'group_label' => 'J', 'position_in_group' => 2, 'description' => 'Bekerja dengan dokumen dan arsip', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 111, 'group_label' => 'J', 'position_in_group' => 3, 'description' => 'Melakukan entri data', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 112, 'group_label' => 'J', 'position_in_group' => 4, 'description' => 'Bekerja sebagai sekretaris', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 113, 'group_label' => 'J', 'position_in_group' => 5, 'description' => 'Mengelola surat dan korespondensi', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 114, 'group_label' => 'J', 'position_in_group' => 6, 'description' => 'Bekerja dengan filing system', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 115, 'group_label' => 'J', 'position_in_group' => 7, 'description' => 'Melakukan scheduling dan appointment', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 116, 'group_label' => 'J', 'position_in_group' => 8, 'description' => 'Bekerja di bagian administrasi', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 117, 'group_label' => 'J', 'position_in_group' => 9, 'description' => 'Mengetik dan membuat laporan', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 118, 'group_label' => 'J', 'position_in_group' => 10, 'description' => 'Bekerja dengan sistem office', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 119, 'group_label' => 'J', 'position_in_group' => 11, 'description' => 'Mengelola inventaris kantor', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 120, 'group_label' => 'J', 'position_in_group' => 12, 'description' => 'Bekerja di back office', 'interest_area' => 'CLERICAL', 'version' => '1995'],

            // ========== GROUP K - PRACTICAL ==========
            ['item_number' => 121, 'group_label' => 'K', 'position_in_group' => 1, 'description' => 'Melakukan pekerjaan praktis sehari-hari', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 122, 'group_label' => 'K', 'position_in_group' => 2, 'description' => 'Bekerja dengan tangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 123, 'group_label' => 'K', 'position_in_group' => 3, 'description' => 'Membuat dan memperbaiki barang', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 124, 'group_label' => 'K', 'position_in_group' => 4, 'description' => 'Bekerja di bidang konstruksi', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 125, 'group_label' => 'K', 'position_in_group' => 5, 'description' => 'Melakukan instalasi dan pemasangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 126, 'group_label' => 'K', 'position_in_group' => 6, 'description' => 'Bekerja sebagai tukang kayu', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 127, 'group_label' => 'K', 'position_in_group' => 7, 'description' => 'Melakukan pekerjaan listrik', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 128, 'group_label' => 'K', 'position_in_group' => 8, 'description' => 'Bekerja di bidang plumbing', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 129, 'group_label' => 'K', 'position_in_group' => 9, 'description' => 'Melakukan maintenance bangunan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 130, 'group_label' => 'K', 'position_in_group' => 10, 'description' => 'Bekerja dengan alat pertukangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 131, 'group_label' => 'K', 'position_in_group' => 11, 'description' => 'Melakukan renovasi', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 132, 'group_label' => 'K', 'position_in_group' => 12, 'description' => 'Bekerja di lapangan praktis', 'interest_area' => 'PRACTICAL', 'version' => '1995'],

            // ========== GROUP L - MEDICAL ==========
            ['item_number' => 133, 'group_label' => 'L', 'position_in_group' => 1, 'description' => 'Merawat pasien dan orang sakit', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 134, 'group_label' => 'L', 'position_in_group' => 2, 'description' => 'Bekerja di rumah sakit atau klinik', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 135, 'group_label' => 'L', 'position_in_group' => 3, 'description' => 'Melakukan diagnosis medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 136, 'group_label' => 'L', 'position_in_group' => 4, 'description' => 'Bekerja sebagai perawat', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 137, 'group_label' => 'L', 'position_in_group' => 5, 'description' => 'Memberikan terapi dan pengobatan', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 138, 'group_label' => 'L', 'position_in_group' => 6, 'description' => 'Bekerja di bidang farmasi medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 139, 'group_label' => 'L', 'position_in_group' => 7, 'description' => 'Melakukan operasi dan prosedur medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 140, 'group_label' => 'L', 'position_in_group' => 8, 'description' => 'Bekerja di laboratorium medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 141, 'group_label' => 'L', 'position_in_group' => 9, 'description' => 'Melakukan rehabilitasi pasien', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 142, 'group_label' => 'L', 'position_in_group' => 10, 'description' => 'Bekerja sebagai dokter', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 143, 'group_label' => 'L', 'position_in_group' => 11, 'description' => 'Melakukan pemeriksaan kesehatan', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 144, 'group_label' => 'L', 'position_in_group' => 12, 'description' => 'Bekerja di bidang kesehatan masyarakat', 'interest_area' => 'MEDICAL', 'version' => '1995'],
        ];
    }
}
