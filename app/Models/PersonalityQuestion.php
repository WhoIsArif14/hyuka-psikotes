<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalityQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['personality_test_id', 'question', 'options', 'order'];

    protected $casts = [
        'options' => 'array',
    ];

    public function test()
    {
        return $this->belongsTo(PersonalityTest::class);
    }
}
