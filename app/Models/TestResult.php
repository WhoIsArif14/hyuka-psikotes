<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'test_id',
        'alat_tes_id',
        'participant_name',
        'start_time',
        'end_time',
        'score',
        'iq',
        'participant_email',
        'education',
        'major',
        'phone_number',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'integer',
        'iq' => 'integer',
    ];

    /**
     * Static helper to compute IQ from raw score and max possible score.
     * Simple linear normalization: maps 0..maxScore -> IQ 70..130 (configurable later).
     */
    public static function computeIq($score, $maxScore)
    {
        $proportion = ($maxScore > 0) ? ($score / $maxScore) : 0;
        $iq = (int) round(70 + ($proportion * 60));

        // Clamp to sensible bounds
        return max(40, min(160, $iq));
    }

    /**
     * Mengembalikan interpretasi IQ sebagai teks.
     */
    public function getIqInterpretationAttribute()
    {
        if (is_null($this->iq)) {
            return null;
        }

        if ($this->iq < 70) {
            return 'Rendah';
        }

        if ($this->iq < 85) {
            return 'Di bawah rata-rata';
        }

        if ($this->iq < 115) {
            return 'Rata-rata';
        }

        if ($this->iq < 130) {
            return 'Di atas rata-rata';
        }

        return 'Sangat tinggi';
    }

    /**
     * Relasi ke model User.
     * Relasi ini opsional, karena user_id bisa null.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Test.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Relasi ke model AlatTes.
     */
    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class);
    }

    /**
     * Relasi ke model UserAnswer.
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }
}
