<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalityResult extends Model
{
    use HasFactory;

    protected $fillable = ['personality_test_id', 'user_id', 'score', 'interpretation', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function test()
    {
        return $this->belongsTo(PersonalityTest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
