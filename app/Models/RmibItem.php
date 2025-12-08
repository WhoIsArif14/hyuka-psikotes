<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RmibItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_number',
        'description',
        'interest_area',
        'version',
    ];

    /**
     * Questions using this RMIB item
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'rmib_item_id');
    }

    /**
     * Get items by interest area
     */
    public static function getByInterestArea($area)
    {
        return self::where('interest_area', $area)
                   ->orderBy('item_number')
                   ->get();
    }

    /**
     * Get all interest areas
     */
    public static function getInterestAreas()
    {
        return [
            'OUTDOOR' => '1. Outdoor (Alam Terbuka)',
            'MECHANICAL' => '2. Mechanical (Mekanik)',
            'COMPUTATIONAL' => '3. Computational (Komputasi)',
            'SCIENTIFIC' => '4. Scientific (Ilmiah)',
            'PERSONAL_CONTACT' => '5. Personal Contact (Kontak Personal)',
            'AESTHETIC' => '6. Aesthetic (Estetika)',
            'LITERARY' => '7. Literary (Sastra)',
            'MUSICAL' => '8. Musical (Musik)',
            'SOCIAL_SERVICE' => '9. Social Service (Layanan Sosial)',
            'CLERICAL' => '10. Clerical (Administrasi)',
            'PRACTICAL' => '11. Practical (Praktis)',
            'MEDICAL' => '12. Medical (Medis)',
        ];
    }

    /**
     * Get interest area name
     */
    public function getInterestAreaNameAttribute()
    {
        $areas = self::getInterestAreas();
        return $areas[$this->interest_area] ?? $this->interest_area;
    }
}