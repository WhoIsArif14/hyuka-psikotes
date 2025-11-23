<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PauliTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'alat_tes_id',
        'total_columns',
        'pairs_per_column',
        'time_per_column',
    ];

    protected $casts = [
        'total_columns' => 'integer',
        'pairs_per_column' => 'integer',
        'time_per_column' => 'integer',
    ];

    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class);
    }

    public function results()
    {
        return $this->hasMany(PauliResult::class);
    }
}

class PauliResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pauli_test_id',
        'test_data',
        'answers',
        'column_performance',
        'total_answers',
        'correct_answers',
        'accuracy',
        'average_speed',
        'completion_time',
    ];

    protected $casts = [
        'test_data' => 'array',
        'answers' => 'array',
        'column_performance' => 'array',
        'total_answers' => 'integer',
        'correct_answers' => 'integer',
        'accuracy' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'completion_time' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pauliTest()
    {
        return $this->belongsTo(PauliTest::class);
    }
}