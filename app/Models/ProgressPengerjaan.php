<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressPengerjaan extends Model
{
    // Gunakan nama tabel eksplisit agar tidak bergantung pada pluralisasi
    protected $table = 'progress_pengerjaan';

    protected $fillable = [
        'user_id',
        'test_id',
        'alat_tes_id',
        'modul_terakhir_id',
        'current_module',
        'percentage',
        'status',
    ];

    protected $casts = [
        'percentage' => 'integer',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function alatTes()
{
    return $this->belongsTo(AlatTes::class); // Asumsi nama Model Anda adalah AlatTes
}

public function modulTerakhir()
{
    return $this->belongsTo(Modul::class, 'modul_terakhir_id'); // Asumsi nama Model Modul
}
} 
