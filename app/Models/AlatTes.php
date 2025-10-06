<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTes extends Model
{
    use HasFactory;

    // Jika nama tabel adalah 'alat_tes'
    protected $table = 'alat_tes';
    
    // ATAU jika nama tabel adalah 'tests', uncomment baris ini:
    // protected $table = 'tests';

    protected $fillable = [
        'name',
        'description',
        'category',
        'duration_minutes',
        'is_active',
        // tambahkan field lain sesuai kebutuhan
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    /**
     * Relasi ke Questions
     * Gunakan 'test_id' sebagai foreign key di tabel questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'test_id');
    }

    /**
     * Scope untuk alat tes yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor untuk jumlah pertanyaan
     */
    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Accessor untuk jumlah pertanyaan pilihan ganda
     */
    public function getPilihanGandaCountAttribute()
    {
        return $this->questions()->where('type', 'PILIHAN_GANDA')->count();
    }

    /**
     * Accessor untuk jumlah pertanyaan essay
     */
    public function getEssayCountAttribute()
    {
        return $this->questions()->where('type', 'ESSAY')->count();
    }

    /**
     * Accessor untuk jumlah pertanyaan hafalan
     */
    public function getHafalanCountAttribute()
    {
        return $this->questions()->where('type', 'HAFALAN')->count();
    }
}