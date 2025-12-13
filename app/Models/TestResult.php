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
        'alat_tes_id', // Ditambahkan untuk tracking alat tes spesifik
        'participant_name', // Ditambahkan untuk peserta tanpa akun
        'start_time',
        'end_time',
        'score',
        'participant_name',
        'participant_email',
        'education',
        'major',
        'phone_number',
    ];

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

