<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RmibItem;
use Illuminate\Support\Facades\DB;

class RmibItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RmibItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = $this->getRmibStandardItems();

        foreach ($items as $item) {
            RmibItem::create($item);
        }

        $this->command->info('✅ 144 RMIB items seeded successfully!');
        $this->command->info('📊 Breakdown:');
        $this->command->info('   - Outdoor: 12 items');
        $this->command->info('   - Mechanical: 12 items');
        $this->command->info('   - Computational: 12 items');
        $this->command->info('   - Scientific: 12 items');
        $this->command->info('   - Personal Contact: 12 items');
        $this->command->info('   - Aesthetic: 12 items');
        $this->command->info('   - Literary: 12 items');
        $this->command->info('   - Musical: 12 items');
        $this->command->info('   - Social Service: 12 items');
        $this->command->info('   - Clerical: 12 items');
        $this->command->info('   - Practical: 12 items');
        $this->command->info('   - Medical: 12 items');
    }

    /**
     * Standard RMIB 144 items (Based on RMIB 1995 - Indonesian Version)
     * Each interest area has 12 items
     */
    private function getRmibStandardItems()
    {
        return [
            // ===== 1. OUTDOOR (Item 1-12) =====
            ['item_number' => 1, 'description' => 'Koreografer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 2, 'description' => 'Administrator', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 3, 'description' => 'Manajer Investasi', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 4, 'description' => 'Jurnalis', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 5, 'description' => 'Automative Engineer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 6, 'description' => 'Dokter Umum', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 7, 'description' => 'Penata Musik / Music Arranger', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 8, 'description' => 'Tour Guide', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 9, 'description' => 'Sales', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 10, 'description' => 'Patissier', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 11, 'description' => 'Ilmuwan', 'interest_area' => 'OUTDOOR', 'version' => '1995'],
            ['item_number' => 12, 'description' => 'Volunteer', 'interest_area' => 'OUTDOOR', 'version' => '1995'],

            // ===== 2. MECHANICAL (Item 13-24) =====
            ['item_number' => 13, 'description' => 'Teller', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 14, 'description' => 'Data Scientist', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 15, 'description' => 'Pengarang Cerita', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 16, 'description' => 'Machine Learning Engineer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 17, 'description' => 'Dokter Spesialis', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 18, 'description' => 'Komposer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 19, 'description' => 'Fotografer', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 20, 'description' => 'Konsultan', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 21, 'description' => 'Pengrajin / Crafter', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 22, 'description' => 'Konservasionis', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 23, 'description' => 'Relawan', 'interest_area' => 'MECHANICAL', 'version' => '1995'],
            ['item_number' => 24, 'description' => 'Penata artistik', 'interest_area' => 'MECHANICAL', 'version' => '1995'],

            // ===== 3. COMPUTATIONAL (Item 25-36) =====
            ['item_number' => 25, 'description' => 'Auditor', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 26, 'description' => 'Reporter', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 27, 'description' => 'Mekanik', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 28, 'description' => 'Radiolog', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 29, 'description' => 'Pemain Alat Musik / Musisi', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 30, 'description' => 'Surveyor', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 31, 'description' => 'Trainer', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 32, 'description' => 'Translator', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 33, 'description' => 'Ahli Meteorologi', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 34, 'description' => 'Psikolog', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 35, 'description' => 'Arsitek', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],
            ['item_number' => 36, 'description' => 'Entry Data', 'interest_area' => 'COMPUTATIONAL', 'version' => '1995'],

            // ===== 4. SCIENTIFIC (Item 37-48) =====
            ['item_number' => 37, 'description' => 'Penulis Buku', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 38, 'description' => 'Product Design Engineer', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 39, 'description' => 'Apoteker', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 40, 'description' => 'Produser Rekaman/Musik', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 41, 'description' => 'Kontraktor', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 42, 'description' => 'Pengacara', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 43, 'description' => 'Komikus', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 44, 'description' => 'Ahli Biologi', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 45, 'description' => 'Konselor', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 46, 'description' => 'Desainer Interior', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 47, 'description' => 'Resepsionis / Front Desk', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],
            ['item_number' => 48, 'description' => 'Programmer', 'interest_area' => 'SCIENTIFIC', 'version' => '1995'],

            // ===== 5. PERSONAL_CONTACT (Item 49-60) =====
            ['item_number' => 49, 'description' => 'Operator Mesin', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 50, 'description' => 'Paramedis', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 51, 'description' => 'Music Programmer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 52, 'description' => 'Pramugari', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 53, 'description' => 'Interviewer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 54, 'description' => 'Food Vlogger', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 55, 'description' => 'Ahli Forensik', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 56, 'description' => 'Perawat / Caregiver', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 57, 'description' => 'Desainer Grafis', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 58, 'description' => 'Kasir', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 59, 'description' => 'Staf Finance', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],
            ['item_number' => 60, 'description' => 'Content Writer', 'interest_area' => 'PERSONAL_CONTACT', 'version' => '1995'],

            // ===== 6. AESTHETIC (Item 61-72) =====
            ['item_number' => 61, 'description' => 'Ahli Gizi', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 62, 'description' => 'Dirigen / Konduktor', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 63, 'description' => 'Ahli Konstruksi', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 64, 'description' => 'Pengusaha Online Shop', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 65, 'description' => 'Content Creator', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 66, 'description' => 'Ahli Botani', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 67, 'description' => 'Pemadam Kebakaran', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 68, 'description' => 'Ilustrator', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 69, 'description' => 'Customer Service', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 70, 'description' => 'Ahli Matematika', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 71, 'description' => 'Penulis Dongeng', 'interest_area' => 'AESTHETIC', 'version' => '1995'],
            ['item_number' => 72, 'description' => 'Aerospace Engineer', 'interest_area' => 'AESTHETIC', 'version' => '1995'],

            // ===== 7. LITERARY (Item 73-84) =====
            ['item_number' => 73, 'description' => 'Pengantar Musik', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 74, 'description' => 'Traveler', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 75, 'description' => 'Public Relation', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 76, 'description' => 'Sinematographer', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 77, 'description' => 'Ahli Astronomi', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 78, 'description' => 'Tim SAR', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 79, 'description' => 'Art Director', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 80, 'description' => 'Aktuaris', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 81, 'description' => 'Financial Planner', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 82, 'description' => 'Technical Writer', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 83, 'description' => 'Desainer Mekanik', 'interest_area' => 'LITERARY', 'version' => '1995'],
            ['item_number' => 84, 'description' => 'Ahli Laboratorium Klinis', 'interest_area' => 'LITERARY', 'version' => '1995'],

            // ===== 8. MUSICAL (Item 85-96) =====
            ['item_number' => 85, 'description' => 'Fasilitator Outbound', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 86, 'description' => 'Influencer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 87, 'description' => 'Hand Crafter', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 88, 'description' => 'Ahli Geologi', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 89, 'description' => 'Pekerja Sosial', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 90, 'description' => 'Fashion Designer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 91, 'description' => 'Personalia', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 92, 'description' => 'Statistician', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 93, 'description' => 'Penulis Karya Ilmiah', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 94, 'description' => 'Research and Development Engineer', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 95, 'description' => 'Fisioterapis', 'interest_area' => 'MUSICAL', 'version' => '1995'],
            ['item_number' => 96, 'description' => 'Penulis Lagu', 'interest_area' => 'MUSICAL', 'version' => '1995'],

            // ===== 9. SOCIAL_SERVICE (Item 97-108) =====
            ['item_number' => 97, 'description' => 'Customer Care', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 98, 'description' => 'Hair Stylist', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 99, 'description' => 'Penulis', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 100, 'description' => 'Tenaga Pengajar', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 101, 'description' => 'Animator', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 102, 'description' => 'Sekretaris', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 103, 'description' => 'Marketing Analyst', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 104, 'description' => 'Advertising Copywriter', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 105, 'description' => 'Robotic Engineer', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 106, 'description' => 'Psikiater', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 107, 'description' => 'Penyanyi', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],
            ['item_number' => 108, 'description' => 'Arkeolog', 'interest_area' => 'SOCIAL_SERVICE', 'version' => '1995'],

            // ===== 10. CLERICAL (Item 109-120) =====
            ['item_number' => 109, 'description' => 'Mengelola administrasi kantor', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 110, 'description' => 'Bekerja dengan dokumen dan arsip', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 111, 'description' => 'Melakukan entri data', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 112, 'description' => 'Bekerja sebagai sekretaris', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 113, 'description' => 'Mengelola surat dan korespondensi', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 114, 'description' => 'Bekerja dengan filing system', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 115, 'description' => 'Melakukan scheduling dan appointment', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 116, 'description' => 'Bekerja di bagian administrasi', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 117, 'description' => 'Mengetik dan membuat laporan', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 118, 'description' => 'Bekerja dengan sistem office', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 119, 'description' => 'Mengelola inventaris kantor', 'interest_area' => 'CLERICAL', 'version' => '1995'],
            ['item_number' => 120, 'description' => 'Bekerja di back office', 'interest_area' => 'CLERICAL', 'version' => '1995'],

            // ===== 11. PRACTICAL (Item 121-132) =====
            ['item_number' => 121, 'description' => 'Melakukan pekerjaan praktis sehari-hari', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 122, 'description' => 'Bekerja dengan tangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 123, 'description' => 'Membuat dan memperbaiki barang', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 124, 'description' => 'Bekerja di bidang konstruksi', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 125, 'description' => 'Melakukan instalasi dan pemasangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 126, 'description' => 'Bekerja sebagai tukang kayu', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 127, 'description' => 'Melakukan pekerjaan listrik', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 128, 'description' => 'Bekerja di bidang plumbing', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 129, 'description' => 'Melakukan maintenance bangunan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 130, 'description' => 'Bekerja dengan alat pertukangan', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 131, 'description' => 'Melakukan renovasi', 'interest_area' => 'PRACTICAL', 'version' => '1995'],
            ['item_number' => 132, 'description' => 'Bekerja di lapangan praktis', 'interest_area' => 'PRACTICAL', 'version' => '1995'],

            // ===== 12. MEDICAL (Item 133-144) =====
            ['item_number' => 133, 'description' => 'Merawat pasien dan orang sakit', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 134, 'description' => 'Bekerja di rumah sakit atau klinik', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 135, 'description' => 'Melakukan diagnosis medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 136, 'description' => 'Bekerja sebagai perawat', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 137, 'description' => 'Memberikan terapi dan pengobatan', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 138, 'description' => 'Bekerja di bidang farmasi medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 139, 'description' => 'Melakukan operasi dan prosedur medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 140, 'description' => 'Bekerja di laboratorium medis', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 141, 'description' => 'Melakukan rehabilitasi pasien', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 142, 'description' => 'Bekerja sebagai dokter', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 143, 'description' => 'Melakukan pemeriksaan kesehatan', 'interest_area' => 'MEDICAL', 'version' => '1995'],
            ['item_number' => 144, 'description' => 'Bekerja di bidang kesehatan masyarakat', 'interest_area' => 'MEDICAL', 'version' => '1995'],
        ];
    }
}