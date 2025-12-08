<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PapiKostickItem extends Model
{
    use HasFactory;

    // Nama tabel di database (asumsi standar Laravel: snake_case dari nama Model)
    protected $table = 'papi_questions'; 

    // Kolom-kolom yang ada di tabel PAPI Kostick Anda.
    // Ini penting agar mass assignment (misalnya saat testing atau seeder) dapat bekerja.
    protected $fillable = [
        'item_number',  // Nomor soal (1 sampai 90)
        'statement_a',  // Pernyataan A
        'statement_b',  // Pernyataan B
        'aspect_a',     // Aspek/Need yang diukur oleh pernyataan A (misal: N/Need to finish)
        'aspect_b',     // Aspek/Need yang diukur oleh pernyataan B (misal: R/Need to belong to groups)
    ];

    // Jika tabel ini tidak menggunakan kolom created_at dan updated_at
    // public $timestamps = false; 

    // Relasi (jika ada, misalnya ke Model Question)
    public function questions()
    {
        return $this->hasMany(Question::class, 'papi_kostick_item_id');
    }
}