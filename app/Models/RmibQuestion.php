<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmibQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'alat_tes_id',
        'item_number',
        'group_title',
        'statement_a', 'statement_b', 'statement_c', 'statement_d',
        'statement_e', 'statement_f', 'statement_g', 'statement_h',
        'statement_i', 'statement_j', 'statement_k', 'statement_l',
        'key_a', 'key_b', 'key_c', 'key_d',
        'key_e', 'key_f', 'key_g', 'key_h',
        'key_i', 'key_j', 'key_k', 'key_l',
    ];

    public function getStatementsArray()
    {
        return [
            'A' => $this->statement_a,
            'B' => $this->statement_b,
            'C' => $this->statement_c,
            'D' => $this->statement_d,
            'E' => $this->statement_e,
            'F' => $this->statement_f,
            'G' => $this->statement_g,
            'H' => $this->statement_h,
            'I' => $this->statement_i,
            'J' => $this->statement_j,
            'K' => $this->statement_k,
            'L' => $this->statement_l,
        ];
    }

    public function getKeysArray()
    {
        return [
            'A' => $this->key_a,
            'B' => $this->key_b,
            'C' => $this->key_c,
            'D' => $this->key_d,
            'E' => $this->key_e,
            'F' => $this->key_f,
            'G' => $this->key_g,
            'H' => $this->key_h,
            'I' => $this->key_i,
            'J' => $this->key_j,
            'K' => $this->key_k,
            'L' => $this->key_l,
        ];
    }

    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function answers()
    {
        return $this->hasMany(RmibAnswer::class);
    }
}