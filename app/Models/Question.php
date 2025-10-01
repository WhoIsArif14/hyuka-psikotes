<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alat_tes_id', // Kolom foreign key baru
        'memory_item_id', // Kolom foreign key baru untuk item memori
        'type',
        'question_text',
        'image_path',
    ];

    public function memoryItem(): BelongsTo
    {
        return $this->belongsTo(MemoryItem::class, 'memory_item_id');
    }

    /**
     * Relasi BARU: Satu Soal dimiliki oleh satu Alat Tes.
     */
    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class);
    }

    /**
     * Relasi ke Opsi Jawaban (tidak berubah).
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}