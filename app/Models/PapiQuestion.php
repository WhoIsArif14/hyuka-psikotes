<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PapiQuestion extends Model
{
    use HasFactory;

    protected $table = 'papi_questions';
    
    // Kolom Role/Need tetap di fillable karena Controller mengisinya dengan NULL
    protected $fillable = [
        'alat_tes_id',
        'item_number',
        'statement_a',
        'statement_b',
        'role_a',
        'need_a',
        'role_b',
        'need_b',
    ];

    protected $casts = [
        'item_number' => 'integer',
    ];

    /**
     * Scope untuk urutan item number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('item_number');
    }

    // SEMUA SCOPE DAN ACCESSOR TERKAIT ROLE DAN NEED DIHAPUS DARI MODEL INI
    // Karena logic pengisian telah disederhanakan menjadi NULL di Controller.

    /**
     * Cek apakah item number valid (1-90)
     */
    public static function isValidItemNumber($number)
    {
        return $number >= 1 && $number <= 90;
    }

    /**
     * Mendapatkan item number yang belum digunakan
     */
    public static function getAvailableItemNumbers()
    {
        $usedNumbers = self::pluck('item_number')->toArray();
        $allNumbers = range(1, 90);
        
        return array_diff($allNumbers, $usedNumbers);
    }

    /**
     * Mendapatkan progress pengisian PAPI (0-100%)
     */
    public static function getProgress()
    {
        $completed = self::count();
        $total = 90;
        
        return [
            'completed' => $completed,
            'total' => $total,
            'remaining' => $total - $completed,
            'percentage' => round(($completed / $total) * 100, 2)
        ];
    }
}