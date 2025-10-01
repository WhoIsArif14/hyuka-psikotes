<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // <-- Tambahkan ini

class ActivationCode extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'test_id',
        'user_id',
        'code',
        'expires_at',
        'completed_at',
        'ip_address',
    ];
    
    // --- LOGIKA BARU UNTUK MEMBUAT KODE OTOMATIS ---
    /**
     * Boot the model.
     * Logika ini akan berjalan setiap kali model ActivationCode dibuat.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activationCode) {
            // Jika kode tidak diisi manual, generate otomatis
            if (empty($activationCode->code)) {
                $activationCode->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Membuat kode unik 4-4 karakter yang belum ada di database.
     */
    private static function generateUniqueCode()
    {
        do {
            // Generate 8 karakter acak, huruf besar, lalu format menjadi XXXX-XXXX
            $code = Str::upper(Str::random(8));
            $formattedCode = substr($code, 0, 4) . '-' . substr($code, 4, 4);
        } while (self::where('code', $formattedCode)->exists()); // Ulangi jika kode sudah ada

        return $formattedCode;
    }
    // --- AKHIR LOGIKA BARU ---


    /**
     * Relasi ke Test.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}