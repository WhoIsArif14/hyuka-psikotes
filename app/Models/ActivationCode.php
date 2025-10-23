<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationCode extends Model
{
    use HasFactory;
    
    // Asumsi tabel Anda adalah 'activation_codes'
    protected $table = 'activation_codes';

    protected $fillable = [
        'code',
        'test_id',
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Relasi ke User (peserta yang menggunakan kode ini).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relasi ke Tes yang diakses kode ini.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
