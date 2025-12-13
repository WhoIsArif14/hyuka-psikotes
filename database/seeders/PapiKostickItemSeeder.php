<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PapiKostickItem;
use Illuminate\Support\Facades\DB;

class PapiKostickItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Data PAPI Kostick lengkap dengan 90 item dan scoring keys
     */
    public function run(): void
    {
        // Hapus data lama
        PapiKostickItem::truncate();

        $items = [
            // Item 1-10: G vs E
            ['item_number' => 1, 'statement_a' => 'Saya seorang pekerja keras', 'statement_b' => 'Saya tidak suka uring-uringan', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'E', 'need_b' => 'N'],
            ['item_number' => 2, 'statement_a' => 'Saya suka menghasilkan pekerjaan yang lebih baik daripada orang lain', 'statement_b' => 'Saya akan tetap menangani suatu pekerjaan sampai selesai', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'E', 'need_b' => 'A'],
            ['item_number' => 3, 'statement_a' => 'Saya suka menunjukkan pada orang lain cara melakukan sesuatu', 'statement_b' => 'Saya ingin berusaha sebaik mungkin', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'E', 'need_b' => 'P'],
            ['item_number' => 4, 'statement_a' => 'Saya suka melucu', 'statement_b' => 'Saya senang memberitahu orang lain hal-hal yang harus dikerjakan', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'E', 'need_b' => 'X'],
            ['item_number' => 5, 'statement_a' => 'Saya suka bergabung dengan kelompok', 'statement_b' => 'Saya senang diperhatikan oleh kelompok', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'E', 'need_b' => 'B'],
            ['item_number' => 6, 'statement_a' => 'Saya suka menjalin hubungan pribadi yang akrab', 'statement_b' => 'Saya suka berteman dengan kelompok', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'E', 'need_b' => 'O'],
            ['item_number' => 7, 'statement_a' => 'Saya dapat cepat berubah jika merasa perlu', 'statement_b' => 'Saya berusaha menjalin hubungan pribadi yang akrab', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'E', 'need_b' => 'Z'],
            ['item_number' => 8, 'statement_a' => 'Saya suka menyerang kembali jika benar-benar disakiti', 'statement_b' => 'Saya suka melakukan hal-hal baru dan berbeda', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'E', 'need_b' => 'K'],
            ['item_number' => 9, 'statement_a' => 'Saya ingin agar atasan menyukai saya', 'statement_b' => 'Saya suka menegur orang lain jika mereka melakukan kesalahan', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'E', 'need_b' => 'F'],
            ['item_number' => 10, 'statement_a' => 'Saya suka mengikuti petunjuk-petunjuk yang diberikan kepada saya', 'statement_b' => 'Saya suka menyenangkan orang-orang yang menjadi atasan saya', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'E', 'need_b' => 'W'],

            // Item 11-20: G vs D
            ['item_number' => 11, 'statement_a' => 'Saya berusaha keras sekali', 'statement_b' => 'Saya seorang yang teratur, saya meletakkan segala sesuatu pada tempatnya', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'D', 'need_b' => 'N'],
            ['item_number' => 12, 'statement_a' => 'Saya dapat membuat orang lain melakukan apa yang saya inginkan', 'statement_b' => 'Saya tidak mudah marah', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'D', 'need_b' => 'A'],
            ['item_number' => 13, 'statement_a' => 'Saya suka memberitahu kelompok, hal-hal yang harus mereka kerjakan', 'statement_b' => 'Saya selalu bertahan pada suatu pekerjaan sampai selesai', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'D', 'need_b' => 'P'],
            ['item_number' => 14, 'statement_a' => 'Saya ingin menjadi orang yang penuh gairah dan menarik', 'statement_b' => 'Saya ingin menjadi orang yang sangat berhasil', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'D', 'need_b' => 'X'],
            ['item_number' => 15, 'statement_a' => 'Saya ingin menjadi bagian kelompok', 'statement_b' => 'Saya suka membantu orang lain mengambil keputusan', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'D', 'need_b' => 'B'],
            ['item_number' => 16, 'statement_a' => 'Saya cemas bila seseorang tidak menyukai saya', 'statement_b' => 'Saya ingin agar orang lain memperhatikan saya', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'D', 'need_b' => 'O'],
            ['item_number' => 17, 'statement_a' => 'Saya suka mencoba hal-hal baru', 'statement_b' => 'Saya lebih suka bekerja sama bersama orang lain daripada sendiri', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'D', 'need_b' => 'Z'],
            ['item_number' => 18, 'statement_a' => 'Kadang kadang saya menyalahkan orang lain jika ada yang tidak beres', 'statement_b' => 'Saya merasa terganggu jika seseorang tidak menyukai saya', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'D', 'need_b' => 'K'],
            ['item_number' => 19, 'statement_a' => 'Saya suka menyenangkan orang yang menjadi atasan saya', 'statement_b' => 'Saya semanang mencoba pekeraan yang baru dan berbeda', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'D', 'need_b' => 'F'],
            ['item_number' => 20, 'statement_a' => 'Saya menyukai petunjuk-petunjuk terperinci untuk melaksanakan tugas', 'statement_b' => 'Saya suka memberitahu orang lain apabila mereka menjengkelkan', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'D', 'need_b' => 'W'],

            // Item 21-30: G vs C
            ['item_number' => 21, 'statement_a' => 'Saya selalu berusaha keras', 'statement_b' => 'Saya selalu melaksanakan setiap langkah dengan sangat hati-hati', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'C', 'need_b' => 'N'],
            ['item_number' => 22, 'statement_a' => 'Saya seorang pemimpin yang baik', 'statement_b' => 'Saya menata pekerjaan dengan baik', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'C', 'need_b' => 'A'],
            ['item_number' => 23, 'statement_a' => 'Saya mudah marah', 'statement_b' => 'Saya lambat dalam membuat keputusan', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'C', 'need_b' => 'P'],
            ['item_number' => 24, 'statement_a' => 'Saya suka mengerjakan beberapa tugas pada saat yang bersamaan', 'statement_b' => 'Saya bila berada dalam satu kelompok, saya suka berdiam diri', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'C', 'need_b' => 'X'],
            ['item_number' => 25, 'statement_a' => 'Saya senang sekali bila diundang', 'statement_b' => 'Saya ingin melakukan sesuatu lebih baik daripada orang lain', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'C', 'need_b' => 'B'],
            ['item_number' => 26, 'statement_a' => 'Saya suka menjalin hubungan pribadi yang akrab', 'statement_b' => 'Saya suka memberi nasihat pada orang lain', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'C', 'need_b' => 'O'],
            ['item_number' => 27, 'statement_a' => 'Saya suka melakukan hal-hal yang baru dan berbeda', 'statement_b' => 'Saya suka menceritakan bagaimana saya berhasil dalam melakukan sesuatu', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'C', 'need_b' => 'Z'],
            ['item_number' => 28, 'statement_a' => 'Apabila pendapat saya benar, saya suka mempertahankannya', 'statement_b' => 'Saya ingin menjadi bagian dari suatu kelompok', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'C', 'need_b' => 'K'],
            ['item_number' => 29, 'statement_a' => 'Saya tidak mau berbeda dari orang lain', 'statement_b' => 'Saya berusaha akrab dengan orang lain', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'C', 'need_b' => 'F'],
            ['item_number' => 30, 'statement_a' => 'Saya senang diberitahu bagaimana melakukan suatu pekerjaan', 'statement_b' => 'Saya mudah bosan', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'C', 'need_b' => 'W'],

            // Item 31-40: G vs R
            ['item_number' => 31, 'statement_a' => 'Saya belerja keras', 'statement_b' => 'Saya banyak berpikir dan membuat rencana', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'R', 'need_b' => 'N'],
            ['item_number' => 32, 'statement_a' => 'Saya memimpin kelompok', 'statement_b' => 'Detail (hal-hal kecil) menarik buat saya', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'R', 'need_b' => 'A'],
            ['item_number' => 33, 'statement_a' => 'Saya membuat keputusan mudah dan cepat', 'statement_b' => 'Saya menyimpan barang-barang secara rapi dan teratur', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'R', 'need_b' => 'P'],
            ['item_number' => 34, 'statement_a' => 'Saya membuat keputusan dengan mudah dan cepat', 'statement_b' => 'Saya jarang marah atau sedih', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'R', 'need_b' => 'X'],
            ['item_number' => 35, 'statement_a' => 'Saya ingin menjadi bagian dalam kelompok', 'statement_b' => 'Saya ingin melakukan hanya satu pekerjaan pada satu waktu', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'R', 'need_b' => 'B'],
            ['item_number' => 36, 'statement_a' => 'Saya berusahan berteman secara akrab', 'statement_b' => 'Saya berusaha keras untuk menjadi yang terbaik', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'R', 'need_b' => 'O'],
            ['item_number' => 37, 'statement_a' => 'Saya suka gaya terbaru dalam hal pakaian dan mobil', 'statement_b' => 'Saya suka bertanggung jawab atas orang lain', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'R', 'need_b' => 'Z'],
            ['item_number' => 38, 'statement_a' => 'Saya senang berdebat', 'statement_b' => 'Saya suka mendapat perhatian', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'R', 'need_b' => 'K'],
            ['item_number' => 39, 'statement_a' => 'Saya suka menyenangkan orang yang menjadi atasan saya', 'statement_b' => 'Saya tertarik untuk menjadi bagian dari kelompok', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'R', 'need_b' => 'F'],
            ['item_number' => 40, 'statement_a' => 'Saya suka mengikuti peraturan dengan hati-hati', 'statement_b' => 'Saya suka orang lain mengenal saya dengan baik', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'R', 'need_b' => 'W'],

            // Item 41-50: G vs S
            ['item_number' => 41, 'statement_a' => 'Saya berusaha keras sekali', 'statement_b' => 'Saya sangat ramah', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'S', 'need_b' => 'N'],
            ['item_number' => 42, 'statement_a' => 'Orang lain berpendapat bahwa saya pemimpin yang baik', 'statement_b' => 'Saya berpikir hati-hati dan lama', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'S', 'need_b' => 'A'],
            ['item_number' => 43, 'statement_a' => 'Saya sering memanfaatkan kesempatan', 'statement_b' => 'Saya suka cerewet mengenai hal-hal yang kecil', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'S', 'need_b' => 'P'],
            ['item_number' => 44, 'statement_a' => 'Orang lain berpendapat bahwa saya bekerja cepat', 'statement_b' => 'Orang lain berpendapat bahwa saya menyimpan segala sesuatu secara teratur dan rapi', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'S', 'need_b' => 'X'],
            ['item_number' => 45, 'statement_a' => 'Saya menyukai permainan dan olah raga', 'statement_b' => 'Saya sangat menyenangkan', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'S', 'need_b' => 'B'],
            ['item_number' => 46, 'statement_a' => 'Saya senang bila orang lain bersikap akrab dan ramah', 'statement_b' => 'Saya selalu berusaha menyelesaikan sesuatu yang telah saya mulai', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'S', 'need_b' => 'O'],
            ['item_number' => 47, 'statement_a' => 'Saya suka bereksperimen dan mencoba hal-hal baru', 'statement_b' => 'Saya suka melaksanakan pekerjaan sulit dengan baik', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'S', 'need_b' => 'Z'],
            ['item_number' => 48, 'statement_a' => 'Saya suka diperlakukan secara adil', 'statement_b' => 'Saya suka memberitahu orang lain cara mengerjakan sesuatu', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'S', 'need_b' => 'K'],
            ['item_number' => 49, 'statement_a' => 'Saya suka melakukan hal-hal yang diharapkan dari saya', 'statement_b' => 'Saya suka mendapat perhatian', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'S', 'need_b' => 'F'],
            ['item_number' => 50, 'statement_a' => 'Saya suka petunjuk-petunjuk terperinci untuk melaksanakan suatu tugas', 'statement_b' => 'Saya senang berada', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'S', 'need_b' => 'W'],

            // Item 51-60: G vs V
            ['item_number' => 51, 'statement_a' => 'Saya selalu berusaha melakukan pekerjan dengan sempurna', 'statement_b' => 'Orang mengatakan bahwa saya hampir tidak pernah lelah', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'V', 'need_b' => 'N'],
            ['item_number' => 52, 'statement_a' => 'Saya tipe seorang pemimpin', 'statement_b' => 'Saya mudah berteman', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'V', 'need_b' => 'A'],
            ['item_number' => 53, 'statement_a' => 'Saya memanfaatkan kesempatan', 'statement_b' => 'Saya banyak sekali berpikir', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'V', 'need_b' => 'P'],
            ['item_number' => 54, 'statement_a' => 'Saya bekerja dengan tempo yang cepat dan mantap', 'statement_b' => 'Saya senang menangani pkerjaan detail', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'V', 'need_b' => 'X'],
            ['item_number' => 55, 'statement_a' => 'Saya memiliki banyak tenaga untuk permainan dan olah raga', 'statement_b' => 'Saya menyimpan segala sesuatu secara rapi dan teratur', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'V', 'need_b' => 'B'],
            ['item_number' => 56, 'statement_a' => 'Saya bergaul dengan semua orang', 'statement_b' => 'Saya berwatak tenang', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'V', 'need_b' => 'O'],
            ['item_number' => 57, 'statement_a' => 'Saya ingin bertemu orang-orang baru dan melakukan hal-hal baru', 'statement_b' => 'Saya selalu ingiin menyelesaikan pekerjaan yag teaalh saya mulai', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'V', 'need_b' => 'Z'],
            ['item_number' => 58, 'statement_a' => 'Saya biasanya suka mempertahankan keyakinan saya', 'statement_b' => 'Saya biasanya suka bekerja keras', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'V', 'need_b' => 'K'],
            ['item_number' => 59, 'statement_a' => 'Saya menyukai saran-saran dan orang-orang yang saya kagumi', 'statement_b' => 'Saya suka bertanggung jawab terhadap perilaku orang lain', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'V', 'need_b' => 'F'],
            ['item_number' => 60, 'statement_a' => 'Saya membiarkan orang lain mempengaruhi diri saya secara kuat', 'statement_b' => 'Saya suka mendapat banyak perhatian', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'V', 'need_b' => 'W'],

            // Item 61-70: G vs T
            ['item_number' => 61, 'statement_a' => 'Saya biasanya bekerja keras sekali', 'statement_b' => 'Saya biasanya bekerja cepat', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'T', 'need_b' => 'N'],
            ['item_number' => 62, 'statement_a' => 'Apabila saya berbicara, kelompok menyimak', 'statement_b' => 'Saya terampil menggunakan peralatan', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'T', 'need_b' => 'A'],
            ['item_number' => 63, 'statement_a' => 'Saya lambat dalam berteman', 'statement_b' => 'Saya lambat dalam mengambil keputusan', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'T', 'need_b' => 'P'],
            ['item_number' => 64, 'statement_a' => 'Saya biasanya makan dengan cepat', 'statement_b' => 'Saya senang membaca', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'T', 'need_b' => 'X'],
            ['item_number' => 65, 'statement_a' => 'Saya menyukai pekerjaan yang membuat saya bergerak', 'statement_b' => 'Saya menyukai pekerjaan yang harus saya kerjakan secara hati-hati', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'T', 'need_b' => 'B'],
            ['item_number' => 66, 'statement_a' => 'Saya berteman dengan sebanyak mungkin orang', 'statement_b' => 'Saya dapat menemukan sesuatu yang telah saya sisihkan', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'T', 'need_b' => 'O'],
            ['item_number' => 67, 'statement_a' => 'Saya merencana jauh dimuka', 'statement_b' => 'Saya selalu menyenangkan', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'T', 'need_b' => 'Z'],
            ['item_number' => 68, 'statement_a' => 'Saya sangat bangga akan nama baik saya', 'statement_b' => 'Saya tetap menangani suatu permasalahan sampai terpecahkan', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'T', 'need_b' => 'K'],
            ['item_number' => 69, 'statement_a' => 'Saya suka menyenangkan orang-orang yang saya kagumi', 'statement_b' => 'Saya ingin berhasil', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'T', 'need_b' => 'F'],
            ['item_number' => 70, 'statement_a' => 'Saya suka orang-orang lain membuat keputusan-keputusan untuk kelompok', 'statement_b' => 'Saya suka membuat keputusan-keputusan untuk kelompok', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'T', 'need_b' => 'W'],

            // Item 71-80: G vs I
            ['item_number' => 71, 'statement_a' => 'Saya selalu berusaha sangat keras', 'statement_b' => 'Saya membuat keputusan secara mudah dan cepat', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'I', 'need_b' => 'N'],
            ['item_number' => 72, 'statement_a' => 'Kelompok biasanya melaksanakan keinginan saya', 'statement_b' => 'Saya biasa sangat tergesa-gesa', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'I', 'need_b' => 'A'],
            ['item_number' => 73, 'statement_a' => 'Saya senang merasa lelah', 'statement_b' => 'Saya lambat dalam membuat keputusan', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'I', 'need_b' => 'P'],
            ['item_number' => 74, 'statement_a' => 'Saya bekerja cepat', 'statement_b' => 'Saya mudah berteman', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'I', 'need_b' => 'X'],
            ['item_number' => 75, 'statement_a' => 'Saya biasanya bersemangat atau bergairah', 'statement_b' => 'Saya mnggunakan banyak wakru unruk berpikir', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'I', 'need_b' => 'B'],
            ['item_number' => 76, 'statement_a' => 'Saya sangat ramah terhadap orang lain', 'statement_b' => 'Saya menyukai pekerjaan yang menuntut ketelitian', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'I', 'need_b' => 'O'],
            ['item_number' => 77, 'statement_a' => 'Saya banyak berpikir dan merencana', 'statement_b' => 'Saya menyimpan segala sesuatu pada tempatnya', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'I', 'need_b' => 'Z'],
            ['item_number' => 78, 'statement_a' => 'Saya menyukai pekerjaan yang menuntut hal-hal yang mendetail', 'statement_b' => 'Saya tidak cepat marah', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'I', 'need_b' => 'K'],
            ['item_number' => 79, 'statement_a' => 'Saya suka mengikuti orang-orang yang saya kagumi', 'statement_b' => 'Saya selalu menyelesaikan pekerjaan yang telah saya mulai', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'I', 'need_b' => 'F'],
            ['item_number' => 80, 'statement_a' => 'Saya menyukai petunjuk-petunjuk yang jelas', 'statement_b' => 'Saya suka bekerja keras', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'I', 'need_b' => 'W'],

            // Item 81-90: G vs L
            ['item_number' => 81, 'statement_a' => 'Saya mengejar hal-hal yang menjadi keinginan saya', 'statement_b' => 'Saya seorang pemimpin yang baik', 'role_a' => 'G', 'need_a' => 'N', 'role_b' => 'L', 'need_b' => 'N'],
            ['item_number' => 82, 'statement_a' => 'Saya membuat orang lain bekerja keras', 'statement_b' => 'Saya suka bersenang-senang', 'role_a' => 'G', 'need_a' => 'A', 'role_b' => 'L', 'need_b' => 'A'],
            ['item_number' => 83, 'statement_a' => 'Saya membuat keputusan dengan cepat', 'statement_b' => 'Saya berbicara cepat', 'role_a' => 'G', 'need_a' => 'P', 'role_b' => 'L', 'need_b' => 'P'],
            ['item_number' => 84, 'statement_a' => 'Saya biasanya bekerja secara tergesa-gesa', 'statement_b' => 'Saya berolah raga secara teratur', 'role_a' => 'G', 'need_a' => 'X', 'role_b' => 'L', 'need_b' => 'X'],
            ['item_number' => 85, 'statement_a' => 'Saya tidak suka bertemu orang-orang lain', 'statement_b' => 'Saya cepat lelah', 'role_a' => 'G', 'need_a' => 'B', 'role_b' => 'L', 'need_b' => 'B'],
            ['item_number' => 86, 'statement_a' => 'Saya berteman dengan banyak sekali orang', 'statement_b' => 'Saya menggunakan banyak waktu untuk berpikir', 'role_a' => 'G', 'need_a' => 'O', 'role_b' => 'L', 'need_b' => 'O'],
            ['item_number' => 87, 'statement_a' => 'Saya suka bekerja dengan teori', 'statement_b' => 'Saya suka melaksanakan pekerjaan detail', 'role_a' => 'G', 'need_a' => 'Z', 'role_b' => 'L', 'need_b' => 'Z'],
            ['item_number' => 88, 'statement_a' => 'Saya suka melaksanakan pekerjaan detail', 'statement_b' => 'Saya suka mengatur pekerjaan saya', 'role_a' => 'G', 'need_a' => 'K', 'role_b' => 'L', 'need_b' => 'K'],
            ['item_number' => 89, 'statement_a' => 'Saya meletakkan segala sesuatu pada tempatnya', 'statement_b' => 'Saya selalu menyenangkan', 'role_a' => 'G', 'need_a' => 'F', 'role_b' => 'L', 'need_b' => 'F'],
            ['item_number' => 90, 'statement_a' => 'Saya senang diberitahu hal-hal yang harus saya kerjakan', 'statement_b' => 'Saya harus menyelesaikan apa yang telah saya mulai', 'role_a' => 'G', 'need_a' => 'W', 'role_b' => 'L', 'need_b' => 'W'],
        ];

        foreach ($items as $item) {
            PapiKostickItem::create($item);
        }

        $this->command->info('✅ Berhasil mengisi 90 item PAPI Kostick lengkap dengan scoring keys!');
    }
}
