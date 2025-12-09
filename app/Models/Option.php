<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    // --- TAMBAHKAN BARIS INI ---
    protected $table = 'options'; 
    // Ini memastikan Model ini selalu merujuk ke tabel 'options', 
    // mengabaikan asumsi nama tabel lain di bagian kode mana pun.
    // ---------------------------

    // Properti Fillable
    protected $fillable = [
        'question_id',
        'option_text',
        'image_path',
        'point',
    ];

    // RELASI: Satu Option dimiliki oleh satu Question
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}