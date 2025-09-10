<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_result_id',
        'question_id',
        'option_id',
    ];

    /**
     * Get the test result that this answer belongs to.
     */
    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }

    // --- RELASI BARU YANG MEMPERBAIKI ERROR ---
    /**
     * Get the option that was chosen for this answer.
     * Setiap jawaban (UserAnswer) dimiliki oleh satu pilihan (Option).
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
