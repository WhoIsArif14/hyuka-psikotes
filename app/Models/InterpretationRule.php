<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterpretationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'min_score',
        'max_score',
        'interpretation_text',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}