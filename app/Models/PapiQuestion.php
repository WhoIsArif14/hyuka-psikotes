<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PapiQuestion extends Model
{
    use HasFactory;

    protected $table = 'papi_questions';
    
    protected $fillable = [
        'item_number',
        'statement_a',
        'statement_b',
        'role_a',
        'need_a',
        'role_b',
        'need_b',
    ];

    protected $casts = [
        'item_number' => 'integer',
    ];

    /**
     * Validation rules untuk Role dan Need
     * Role: L (Leader), F (Follower)
     * Need: N, G, A, R, T, V, X, S, B, O, Z, K, F, W, P, D, C, E
     */
    public static $validRoles = ['L', 'F'];
    
    public static $validNeeds = [
        'N', // Need to Finish Task
        'G', // Need to Achieve
        'A', // Need to Be Noticed
        'R', // Need to Belong to Groups
        'T', // Need for Intimacy
        'V', // Need for Rules and Supervision
        'X', // Need to be Popular
        'S', // Need for Self-Achievement
        'B', // Need for Control
        'O', // Need for Routine
        'Z', // Need for Change
        'K', // Need to Be Supportive
        'F', // Need for Orderliness
        'W', // Need for Power
        'P', // Need to Relate Closely
        'D', // Need to Be Aggressive
        'C', // Need to Be Cautious
        'E', // Need for Attention from Authority
    ];

    /**
     * Scope untuk urutan item number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('item_number');
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->where('role_a', strtoupper($role))
              ->orWhere('role_b', strtoupper($role));
        });
    }

    /**
     * Scope untuk filter berdasarkan need
     */
    public function scopeByNeed($query, $need)
    {
        return $query->where(function($q) use ($need) {
            $q->where('need_a', strtoupper($need))
              ->orWhere('need_b', strtoupper($need));
        });
    }

    /**
     * Accessor untuk mendapatkan nama lengkap Role A
     */
    public function getRoleANameAttribute()
    {
        return $this->role_a === 'L' ? 'Leader' : 'Follower';
    }

    /**
     * Accessor untuk mendapatkan nama lengkap Role B
     */
    public function getRoleBNameAttribute()
    {
        return $this->role_b === 'L' ? 'Leader' : 'Follower';
    }

    /**
     * Accessor untuk mendapatkan deskripsi Need A
     */
    public function getNeedADescriptionAttribute()
    {
        return self::getNeedDescription($this->need_a);
    }

    /**
     * Accessor untuk mendapatkan deskripsi Need B
     */
    public function getNeedBDescriptionAttribute()
    {
        return self::getNeedDescription($this->need_b);
    }

    /**
     * Static method untuk mendapatkan deskripsi Need
     */
    public static function getNeedDescription($need)
    {
        $descriptions = [
            'N' => 'Need to Finish Task',
            'G' => 'Need to Achieve',
            'A' => 'Need to Be Noticed',
            'R' => 'Need to Belong to Groups',
            'T' => 'Need for Intimacy',
            'V' => 'Need for Rules and Supervision',
            'X' => 'Need to be Popular',
            'S' => 'Need for Self-Achievement',
            'B' => 'Need for Control',
            'O' => 'Need for Routine',
            'Z' => 'Need for Change',
            'K' => 'Need to Be Supportive',
            'F' => 'Need for Orderliness',
            'W' => 'Need for Power',
            'P' => 'Need to Relate Closely',
            'D' => 'Need to Be Aggressive',
            'C' => 'Need to Be Cautious',
            'E' => 'Need for Attention from Authority',
        ];

        return $descriptions[strtoupper($need)] ?? 'Unknown Need';
    }

    /**
     * Cek apakah item number valid (1-90)
     */
    public static function isValidItemNumber($number)
    {
        return $number >= 1 && $number <= 90;
    }

    /**
     * Mendapatkan item number yang belum digunakan
     */
    public static function getAvailableItemNumbers()
    {
        $usedNumbers = self::pluck('item_number')->toArray();
        $allNumbers = range(1, 90);
        
        return array_diff($allNumbers, $usedNumbers);
    }

    /**
     * Mendapatkan progress pengisian PAPI (0-100%)
     */
    public static function getProgress()
    {
        $completed = self::count();
        $total = 90;
        
        return [
            'completed' => $completed,
            'total' => $total,
            'remaining' => $total - $completed,
            'percentage' => round(($completed / $total) * 100, 2)
        ];
    }
}