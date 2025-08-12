<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'test_category_id',
        'title',
        'description',
        'duration_minutes',
        'is_published',
    ];

    // RELASI: Satu Test dimiliki oleh satu TestCategory
    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    // RELASI: Satu Test memiliki banyak Question
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // RELASI: Satu Test memiliki banyak TestResult
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}