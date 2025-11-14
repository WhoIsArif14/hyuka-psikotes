<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExampleQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'alat_tes_id',
        'question_type',
        'title',
        'question_text',
        'options',
        'correct_answer_index',
        'answer_example',
        'memory_content',
        'memory_duration',
    ];

    protected $casts = [
        'correct_answer_index' => 'integer',
        'memory_duration' => 'integer',
    ];

    /**
     * Relationship dengan AlatTes
     */
    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    /**
     * Get decoded options
     */
    public function getOptionsArrayAttribute()
    {
        return $this->options ? json_decode($this->options, true) : [];
    }
}