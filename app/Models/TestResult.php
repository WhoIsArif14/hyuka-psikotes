<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResult extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'user_id',
        'test_id',
        'start_time',
        'end_time',
        'score',
    ];

    // RELASI: Satu TestResult dimiliki oleh satu User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // RELASI: Satu TestResult dimiliki oleh satu Test
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    // RELASI: Satu TestResult memiliki banyak UserAnswer
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }
}