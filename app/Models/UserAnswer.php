<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'question_id',
        'option_id',
        'file_path',
    ];

    /**
     * Relasi ke hasil tes.
     */
    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }

    /**
     * Relasi ke pertanyaan.
     * (Direkomendasikan menambahkan ini agar lebih mudah memanggil data)
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // ❌ Hapus / nonaktifkan relasi Option,
    // karena sistem sekarang menyimpan opsi dalam kolom JSON `questions.options`
    //
    // public function option(): BelongsTo
    // {
    //     return $this->belongsTo(Option::class);
    // }
}
