<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'alat_tes_id', // Ada 2 foreign key di tabel
        'memory_item_id',
        'type',
        'question_text',
        'image_path',
        'options',
        'correct_answer_index',
        'memory_content',
        'memory_type',
        'duration_seconds',
    ];

    protected $casts = [
        'options' => 'array', // Otomatis convert JSON ke array
        'correct_answer_index' => 'integer',
        'duration_seconds' => 'integer',
    ];

    /**
     * Relasi ke AlatTes menggunakan test_id
     */
    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class, 'test_id');
    }

    /**
     * Relasi alternatif menggunakan alat_tes_id (jika dipakai)
     */
    public function alatTesAlt()
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    /**
     * Relasi ke MemoryItem (jika ada model ini)
     */
    public function memoryItem()
    {
        return $this->belongsTo(MemoryItem::class);
    }

    /**
     * Accessor untuk mendapatkan opsi sebagai array
     */
    public function getOptionsArrayAttribute()
    {
        if (is_string($this->options)) {
            return json_decode($this->options, true);
        }
        return $this->options;
    }

    /**
     * Accessor untuk mendapatkan jawaban benar
     */
    public function getCorrectAnswerAttribute()
    {
        if ($this->type === 'PILIHAN_GANDA' && $this->options && isset($this->correct_answer_index)) {
            $options = is_string($this->options) ? json_decode($this->options, true) : $this->options;
            return $options[$this->correct_answer_index]['text'] ?? null;
        }
        return null;
    }

    /**
     * Mutator untuk normalize type value
     */
    public function setTypeAttribute($value)
    {
        // Convert old value to new format
        $typeMap = [
            'multiple_choice' => 'PILIHAN_GANDA',
            'essay' => 'ESSAY',
            'memory' => 'HAFALAN',
        ];

        $this->attributes['type'] = $typeMap[$value] ?? strtoupper($value);
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk pertanyaan pilihan ganda
     */
    public function scopePilihanGanda($query)
    {
        return $query->whereIn('type', ['PILIHAN_GANDA', 'multiple_choice']);
    }

    /**
     * Scope untuk pertanyaan essay
     */
    public function scopeEssay($query)
    {
        return $query->whereIn('type', ['ESSAY', 'essay']);
    }

    /**
     * Scope untuk pertanyaan hafalan
     */
    public function scopeHafalan($query)
    {
        return $query->whereIn('type', ['HAFALAN', 'memory']);
    }
}