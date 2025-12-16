<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlatTes extends Model
{
    use HasFactory;

    protected $table = 'alat_tes';
    
    protected $fillable = [
        'name',
        'duration_minutes',
        'slug',
        'description',
        'instructions',
        'example_questions',
    ];

    protected $casts = [
        'example_questions' => 'array',
        'duration_minutes' => 'integer',
    ];

    /**
     * Relationship ke Questions (Soal Umum)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'alat_tes_id');
    }

    /**
     * Relationship ke PapiQuestions
     */
    public function papiQuestions(): HasMany
    {
        return $this->hasMany(PapiQuestion::class, 'alat_tes_id');
    }

    /**
     * ✅ RELASI MANY-TO-MANY YANG DITAMBAHKAN
     * Relasi Many-to-Many: Satu Alat Tes bisa digunakan di banyak Modul (Test).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(
            Test::class, 
            'modul_alat_tes',    // nama tabel pivot
            'alat_tes_id',        // foreign key untuk AlatTes di tabel pivot
            'test_id'             // foreign key untuk Test di tabel pivot
        )->withTimestamps();      // jika ada created_at, updated_at di pivot
    }

    /**
     * Mengecek apakah alat tes ini adalah PAPI Kostick
     * 
     * @return bool
     */
    public function isPapiKostick(): bool
    {
        // Cek slug dulu
        if (isset($this->slug) && !empty($this->slug)) {
            $slug = strtolower(trim($this->slug));
            
            if (in_array($slug, [
                'papi-kostick',
                'papikostick',
                'papi_kostick',
                'papi kostick'
            ])) {
                return true;
            }
        }
        
        // Cek name jika slug tidak ada/tidak match
        if (isset($this->name) && !empty($this->name)) {
            $name = strtolower(trim($this->name));
            
            if (str_contains($name, 'papi') || 
                str_contains($name, 'kostick') || 
                str_contains($name, 'mami')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Mendapatkan jumlah total soal dengan filter alat_tes_id
     * 
     * @return int
     */
    public function getTotalQuestionsAttribute(): int
    {
        if ($this->isPapiKostick()) {
            return PapiQuestion::where('alat_tes_id', $this->id)->count();
        }
        
        return $this->questions()->count();
    }

    /**
     * Mendapatkan semua soal dengan pagination dan filter alat_tes_id
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuestionsPaginated($perPage = 10)
    {
        if ($this->isPapiKostick()) {
            return PapiQuestion::where('alat_tes_id', $this->id)
                              ->orderBy('item_number')
                              ->paginate($perPage);
        }
        
        return $this->questions()->paginate($perPage);
    }

    /**
     * Helper untuk mendapatkan semua soal (tanpa pagination)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllQuestions()
    {
        if ($this->isPapiKostick()) {
            return PapiQuestion::where('alat_tes_id', $this->id)
                              ->orderBy('item_number')
                              ->get();
        }
        
        return $this->questions;
    }
}