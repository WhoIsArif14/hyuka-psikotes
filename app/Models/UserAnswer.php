<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'test_result_id',
        'question_id',
        'option_id',
    ];

    // RELASI: Satu UserAnswer dimiliki oleh satu TestResult
    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }
}