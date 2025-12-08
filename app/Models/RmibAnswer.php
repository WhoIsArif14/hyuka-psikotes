<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmibAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alat_tes_id',
        'rmib_question_id',
        'rank_a', 'rank_b', 'rank_c', 'rank_d',
        'rank_e', 'rank_f', 'rank_g', 'rank_h',
        'rank_i', 'rank_j', 'rank_k', 'rank_l',
    ];

    public function getRanksArray()
    {
        return [
            'A' => $this->rank_a,
            'B' => $this->rank_b,
            'C' => $this->rank_c,
            'D' => $this->rank_d,
            'E' => $this->rank_e,
            'F' => $this->rank_f,
            'G' => $this->rank_g,
            'H' => $this->rank_h,
            'I' => $this->rank_i,
            'J' => $this->rank_j,
            'K' => $this->rank_k,
            'L' => $this->rank_l,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(RmibQuestion::class, 'rmib_question_id');
    }
}