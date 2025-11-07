<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheatingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'test_result_id',
        'violation_type',
        'description',
        'ip_address',
        'user_agent',
        'violation_count',
        'detected_at',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(AlatTes::class, 'test_id');
    }

    public function testResult()
    {
        return $this->belongsTo(TestResult::class);
    }

    // Helper Methods
    public static function logViolation($userId, $testId, $violationType, $description = null, $testResultId = null)
    {
        return self::create([
            'user_id' => $userId,
            'test_id' => $testId,
            'test_result_id' => $testResultId,
            'violation_type' => $violationType,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'detected_at' => now(),
        ]);
    }

    public static function getViolationCount($userId, $testId, $violationType = null)
    {
        $query = self::where('user_id', $userId)
                    ->where('test_id', $testId);
        
        if ($violationType) {
            $query->where('violation_type', $violationType);
        }

        return $query->sum('violation_count');
    }

    public static function hasExceededLimit($userId, $testId, $maxViolations = 5)
    {
        return self::getViolationCount($userId, $testId) >= $maxViolations;
    }
}