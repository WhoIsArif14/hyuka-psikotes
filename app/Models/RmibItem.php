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
        'group_label',           // ✅ TAMBAHKAN INI
        'position_in_group',     // ✅ TAMBAHKAN INI
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
     * ✅ TAMBAHKAN: Get items grouped by group_label
     */
    public static function getGroupedItems()
    {
        return self::orderBy('group_label')
            ->orderBy('position_in_group')
            ->get()
            ->groupBy('group_label');
    }

    /**
     * ✅ TAMBAHKAN: Get all unique group labels
     */
    public static function getGroupLabels()
    {
        return self::distinct()
            ->pluck('group_label')
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * ✅ TAMBAHKAN: Get items by group label
     */
    public static function getByGroupLabel($groupLabel)
    {
        return self::where('group_label', $groupLabel)
            ->orderBy('position_in_group')
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
     * ✅ TAMBAHKAN: Get group label mapping
     */
    public static function getGroupLabelMapping()
    {
        return [
            'A' => 'OUTDOOR',
            'B' => 'MECHANICAL',
            'C' => 'COMPUTATIONAL',
            'D' => 'SCIENTIFIC',
            'E' => 'PERSONAL_CONTACT',
            'F' => 'AESTHETIC',
            'G' => 'LITERARY',
            'H' => 'MUSICAL',
            'I' => 'SOCIAL_SERVICE',
            'J' => 'CLERICAL',
            'K' => 'PRACTICAL',
            'L' => 'MEDICAL',
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

    /**
     * ✅ TAMBAHKAN: Get group name (A, B, C, etc.)
     */
    public function getGroupNameAttribute()
    {
        return $this->group_label ?? 'N/A';
    }
}