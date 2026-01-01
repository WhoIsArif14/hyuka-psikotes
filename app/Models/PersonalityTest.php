<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalityTest extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function questions()
    {
        return $this->hasMany(PersonalityQuestion::class)->orderBy('order');
    }

    public function results()
    {
        return $this->hasMany(PersonalityResult::class);
    }
}
