<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressPengerjaan extends Model
{
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
