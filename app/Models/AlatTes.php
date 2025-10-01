<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AlatTes extends Model
{
    use HasFactory;

    // Nama tabel eksplisit karena nama class 'AlatTes' menjadi 'alat_tes'
    protected $table = 'alat_tes';

    protected $fillable = ['name', 'duration_minutes'];

    /**
     * Relasi: Satu Alat Tes memiliki banyak Soal (Question).
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relasi BARU: Satu Alat Tes memiliki banyak Item Memori.
     * Item memori adalah materi yang harus dihafal oleh peserta tes.
     */
    public function memoryItems(): HasMany
    {
        return $this->hasMany(MemoryItem::class, 'alat_tes_id');
    }

    /**
     * Relasi: Satu Alat Tes bisa digunakan di banyak Modul (Test).
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'modul_alat_tes');
    }
}