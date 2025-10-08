<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoryItem extends Model
{
    use HasFactory;

    /**
     * Tipe konten: 'TEXT', 'IMAGE', 'SEQUENCE', dll.
     */
    const TYPE_TEXT = 'TEXT';
    const TYPE_IMAGE = 'IMAGE';

    protected $fillable = [
        'alat_tes_id',
        'content', // Konten yang harus dihafal (teks atau path gambar)
        'type',    // Jenis konten: TEXT atau IMAGE
        'duration_seconds', // Durasi tampil (dalam detik)
        'order',   // Urutan item dalam satu sesi tes hafalan
    ];

    /**
     * Relasi: Satu item memori bisa memiliki banyak pertanyaan (Recall Questions).
     */
    public function recallQuestions(): HasMany
    {
        // Mengasumsikan ada 'memory_item_id' di tabel questions
        return $this->hasMany(Question::class, 'memory_item_id');
    }

    /**
     * Relasi: Item ini dimiliki oleh Alat Tes tertentu.
     */
    public function AlatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }
}
