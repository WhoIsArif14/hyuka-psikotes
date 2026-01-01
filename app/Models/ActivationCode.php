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
        // new batch fields
        'batch_code',
        'batch_name',
        'status',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
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
