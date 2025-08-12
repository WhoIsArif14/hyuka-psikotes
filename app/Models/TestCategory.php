<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestCategory extends Model
{
    use HasFactory;

    // Properti Fillable
    protected $fillable = [
        'name',
        'description',
    ];

    // RELASI: Satu TestCategory memiliki banyak Test
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }
}