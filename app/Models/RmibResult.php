<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'interest_ranking', // Menyimpan ranking 12 minat dalam format JSON
        'top_interest_1',
        'top_interest_2',
        'top_interest_3',
        'completed_at',
    ];

    protected $casts = [
        // interest_ranking harus di-cast ke array karena merupakan JSON
        'interest_ranking' => 'array', 
        'completed_at' => 'datetime',
    ];

    /**
     * Get all scores as array with category codes.
     */
    public function getScoresArray(): array
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
     * Get interest category name and translation.
     */
    public static function getInterestName(string $code): string
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
     * Map interest code to score column name (e.g., 'Mu' -> 'score_musical').
     */
    private function mapCodeToColumn(string $code): string
    {
        $map = [
            'O' => 'outdoor', 'M' => 'mechanical', 'C' => 'computational', 
            'S' => 'scientific', 'P' => 'personal', 'A' => 'aesthetic', 
            'L' => 'literary', 'Mu' => 'musical', 'SS' => 'social', 
            'Cl' => 'clerical', 'Pr' => 'practical', 'Me' => 'medical'
        ];
        return 'score_' . ($map[$code] ?? '');
    }

    /**
     * Get top 3 interests with codes, names, and scores.
     */
    public function getTopInterests(): array
    {
        // Mendefinisikan 3 kolom top interest
        $topColumns = [
            'top_interest_1' => 1,
            'top_interest_2' => 2,
            'top_interest_3' => 3,
        ];

        $topInterests = [];

        foreach ($topColumns as $column => $rank) {
            $code = $this->{$column};
            // Pastikan kode ada sebelum mencoba mencari skor dan nama
            if ($code) {
                $scoreColumn = $this->mapCodeToColumn($code);
                $topInterests[] = [
                    'rank' => $rank,
                    'code' => $code,
                    'name' => self::getInterestName($code),
                    // Mengakses skor dinamis (misal: $this->score_musical)
                    'score' => $this->{$scoreColumn} ?? 0, 
                ];
            }
        }
        
        return $topInterests;
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke AlatTes
     */
    public function alatTes()
    {
        // Ganti AlatTes::class dengan model yang sesuai jika berbeda
        return $this->belongsTo(AlatTes::class, 'alat_tes_id'); 
    }
}