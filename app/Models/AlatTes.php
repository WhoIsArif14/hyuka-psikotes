<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTes extends Model
{
    use HasFactory;

    protected $table = 'alat_tes';
    
    protected $fillable = [
        'name',
        'duration_minutes',
        'slug',
        'description',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
    ];

    /**
     * Relationship ke Questions (Soal Umum)
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'alat_tes_id');
    }

    /**
     * ✅ TAMBAHAN: Relationship ke PapiQuestions
     */
    public function papiQuestions()
    {
        return $this->hasMany(PapiQuestion::class, 'alat_tes_id');
    }

    /**
     * Mengecek apakah alat tes ini adalah PAPI Kostick
     * 
     * @return bool
     */
    public function isPapiKostick()
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
        
        // ✅ Cek name jika slug tidak ada/tidak match
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
     * ✅ PERBAIKAN: Mendapatkan jumlah total soal dengan filter alat_tes_id
     * 
     * @return int
     */
    public function getTotalQuestionsAttribute()
    {
        if ($this->isPapiKostick()) {
            // ✅ Filter berdasarkan alat_tes_id
            return PapiQuestion::where('alat_tes_id', $this->id)->count();
        }
        
        return $this->questions()->count();
    }

    /**
     * ✅ PERBAIKAN: Mendapatkan semua soal dengan pagination dan filter alat_tes_id
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuestionsPaginated($perPage = 10)
    {
        if ($this->isPapiKostick()) {
            // ✅ Filter berdasarkan alat_tes_id
            return PapiQuestion::where('alat_tes_id', $this->id)
                              ->orderBy('item_number')
                              ->paginate($perPage);
        }
        
        return $this->questions()->paginate($perPage);
    }

    /**
     * ✅ TAMBAHAN: Helper untuk mendapatkan semua soal (tanpa pagination)
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