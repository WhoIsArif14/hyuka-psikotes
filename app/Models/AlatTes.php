<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatTes extends Model
{
    use HasFactory;

    protected $table = 'alat_tes';
    
    // PENTING: Hanya masukkan field yang ADA di tabel database Anda
    protected $fillable = [
        'name',
        'duration_minutes',
        'slug',           // Tambahkan jika kolom ini ada di database
        'description',    // Tambahkan jika kolom ini ada di database
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
     * Mengecek apakah alat tes ini adalah PAPI Kostick
     * 
     * @return bool
     */
    public function isPapiKostick()
    {
        if (!isset($this->slug)) {
            return false;
        }
        
        $slug = strtolower(trim($this->slug));
        
        // Cek berbagai kemungkinan format slug PAPI
        return in_array($slug, [
            'papi-kostick',
            'papikostick',
            'papi_kostick',
            'papi kostick'
        ]);
    }

    /**
     * Mendapatkan jumlah total soal
     * (Baik dari questions maupun papi_questions)
     * 
     * @return int
     */
    public function getTotalQuestionsAttribute()
    {
        if ($this->isPapiKostick()) {
            return PapiQuestion::count();
        }
        
        return $this->questions()->count();
    }

    /**
     * Mendapatkan semua soal dengan pagination
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuestionsPaginated($perPage = 10)
    {
        if ($this->isPapiKostick()) {
            return PapiQuestion::orderBy('item_number')->paginate($perPage);
        }
        
        return $this->questions()->paginate($perPage);
    }
}