<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'question_id',
        'option_text',
        'image_path',
        'point',
    ];

    // RELASI: Satu Option dimiliki oleh satu Question
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}