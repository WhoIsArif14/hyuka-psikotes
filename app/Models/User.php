<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'role', // Tambahkan 'role' di sini
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // RELASI: Satu User memiliki banyak TestResult
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    // RELASI: Hasil Tes Terbaru (relasi hasOne untuk mempermudah eager loading)
    public function latestTestResult(): HasOne
    {
        // Menggunakan latestOfMany() untuk mengambil hasil terbaru per user
        return $this->hasOne(TestResult::class)->latestOfMany();
    }

    /**
     * Relasi: progress pengerjaan terkini (dipakai untuk menampilkan apa yang sedang dikerjakan peserta)
     * Mengembalikan progress dengan status 'On Progress' jika ada.
     */
    public function testProgress(): HasOne
    {
        return $this->hasOne(\App\Models\ProgressPengerjaan::class)->where('status', 'On Progress');
    }
}
