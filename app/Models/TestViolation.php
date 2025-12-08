<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'violation_type',
        'details',
        'user_agent',
        'ip_address',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Test
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Scope untuk filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter by test
     */
    public function scopeByTest($query, $testId)
    {
        return $query->where('test_id', $testId);
    }

    /**
     * Scope untuk violations hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope untuk violations dalam rentang waktu tertentu
     */
    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Get violation type label
     */
    public function getTypeLabel()
    {
        $labels = [
            'screenshot_attempt' => 'Percobaan Screenshot',
            'tab_switch' => 'Pindah Tab',
            'window_blur' => 'Kehilangan Fokus Window',
            'right_click' => 'Klik Kanan',
            'devtools_attempt' => 'Buka Developer Tools',
            'devtools_open' => 'Developer Tools Terbuka',
            'copy_attempt' => 'Percobaan Copy',
            'exit_fullscreen' => 'Keluar dari Fullscreen',
        ];

        return $labels[$this->violation_type] ?? ucfirst(str_replace('_', ' ', $this->violation_type));
    }

    /**
     * Get severity color
     */
    public function getSeverityColor()
    {
        $highSeverity = ['screenshot_attempt', 'devtools_open', 'tab_switch'];
        $mediumSeverity = ['right_click', 'copy_attempt', 'exit_fullscreen'];

        if (in_array($this->violation_type, $highSeverity)) {
            return 'red';
        } elseif (in_array($this->violation_type, $mediumSeverity)) {
            return 'yellow';
        } else {
            return 'gray';
        }
    }
}