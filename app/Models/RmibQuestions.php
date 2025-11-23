<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// =========================================
// MODEL 1: RmibQuestion
// =========================================
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

    /**
     * Get all statements as array
     */
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

    /**
     * Get all keys as array
     */
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

// =========================================
// MODEL 2: RmibAnswer
// =========================================
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

    /**
     * Get ranks as array
     */
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

// =========================================
// MODEL 3: RmibResult
// =========================================
class RmibResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alat_tes_id',
        'score_outdoor',
        'score_mechanical',
        'score_computational',
        'score_scientific',
        'score_personal',
        'score_aesthetic',
        'score_literary',
        'score_musical',
        'score_social',
        'score_clerical',
        'score_practical',
        'score_medical',
        'interest_ranking',
        'top_interest_1',
        'top_interest_2',
        'top_interest_3',
        'completed_at',
    ];

    protected $casts = [
        'interest_ranking' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get all scores as array
     */
    public function getScoresArray()
    {
        return [
            'O' => ['name' => 'Outdoor', 'score' => $this->score_outdoor],
            'M' => ['name' => 'Mechanical', 'score' => $this->score_mechanical],
            'C' => ['name' => 'Computational', 'score' => $this->score_computational],
            'S' => ['name' => 'Scientific', 'score' => $this->score_scientific],
            'P' => ['name' => 'Personal Contact', 'score' => $this->score_personal],
            'A' => ['name' => 'Aesthetic', 'score' => $this->score_aesthetic],
            'L' => ['name' => 'Literary', 'score' => $this->score_literary],
            'Mu' => ['name' => 'Musical', 'score' => $this->score_musical],
            'SS' => ['name' => 'Social Service', 'score' => $this->score_social],
            'Cl' => ['name' => 'Clerical', 'score' => $this->score_clerical],
            'Pr' => ['name' => 'Practical', 'score' => $this->score_practical],
            'Me' => ['name' => 'Medical', 'score' => $this->score_medical],
        ];
    }

    /**
     * Get interest category name
     */
    public static function getInterestName($code)
    {
        $interests = [
            'O' => 'Outdoor (Luar Ruangan)',
            'M' => 'Mechanical (Mekanik)',
            'C' => 'Computational (Komputasi)',
            'S' => 'Scientific (Ilmiah)',
            'P' => 'Personal Contact (Kontak Personal)',
            'A' => 'Aesthetic (Estetika)',
            'L' => 'Literary (Sastra)',
            'Mu' => 'Musical (Musik)',
            'SS' => 'Social Service (Layanan Sosial)',
            'Cl' => 'Clerical (Administrasi)',
            'Pr' => 'Practical (Praktis)',
            'Me' => 'Medical (Medis)',
        ];

        return $interests[$code] ?? $code;
    }

    /**
     * Get top 3 interests with descriptions
     */
    public function getTopInterests()
    {
        return [
            [
                'rank' => 1,
                'code' => $this->top_interest_1,
                'name' => self::getInterestName($this->top_interest_1),
                'score' => $this->{'score_' . strtolower(str_replace(['Mu', 'SS', 'Cl', 'Pr', 'Me'], ['musical', 'social', 'clerical', 'practical', 'medical'], $this->top_interest_1))},
            ],
            [
                'rank' => 2,
                'code' => $this->top_interest_2,
                'name' => self::getInterestName($this->top_interest_2),
                'score' => $this->{'score_' . strtolower(str_replace(['Mu', 'SS', 'Cl', 'Pr', 'Me'], ['musical', 'social', 'clerical', 'practical', 'medical'], $this->top_interest_2))},
            ],
            [
                'rank' => 3,
                'code' => $this->top_interest_3,
                'name' => self::getInterestName($this->top_interest_3),
                'score' => $this->{'score_' . strtolower(str_replace(['Mu', 'SS', 'Cl', 'Pr', 'Me'], ['musical', 'social', 'clerical', 'practical', 'medical'], $this->top_interest_3))},
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }
}