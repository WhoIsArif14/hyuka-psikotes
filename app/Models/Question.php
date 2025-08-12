<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'test_id',
        'question_text',
        'image_path',
    ];

    // RELASI: Satu Question dimiliki oleh satu Test
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    // RELASI: Satu Question memiliki banyak Option
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}