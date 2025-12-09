<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'alat_tes_id',
        'test_id',
        'type',
        'image_path',
        'question_text',
        'example_question',
        'instructions',
        'memory_content',
        'memory_type',
        'duration_seconds',
        'options',
        'correct_answer_index',
        'correct_answers',       // ✅ NEW - untuk multiple answers
        'ranking_category',
        'ranking_weight',
        'metadata',
    ];

    protected $casts = [
        'options' => 'json',
        'correct_answers' => 'json', // ✅ NEW - cast JSON array
        'metadata' => 'json',
    ];

    // ✅ ACCESSOR - Mendapatkan correct answers (baik single atau multiple)
    public function getCorrectAnswersArray()
    {
        // Jika multiple answers (PILIHAN_GANDA_KOMPLEKS)
        if ($this->correct_answers) {
            return is_array($this->correct_answers) 
                ? $this->correct_answers 
                : json_decode($this->correct_answers, true);
        }
        
        // Jika single answer (PILIHAN_GANDA)
        if ($this->correct_answer_index !== null) {
            return [$this->correct_answer_index];
        }
        
        return [];
    }

    // ✅ HELPER - Check apakah soal punya multiple answers
    public function hasMultipleCorrectAnswers()
    {
        return $this->type === 'PILIHAN_GANDA_KOMPLEKS' && !is_null($this->correct_answers);
    }

    // ✅ HELPER - Check apakah jawaban peserta benar
    public function checkAnswer($userAnswer)
    {
        if ($this->hasMultipleCorrectAnswers()) {
            // User harus memilih SEMUA jawaban yang benar
            $correctAnswers = $this->getCorrectAnswersArray();
            $userAnswerArray = is_array($userAnswer) ? $userAnswer : [$userAnswer];
            
            // Bandingkan: user answer harus sama persis dengan correct answers
            sort($correctAnswers);
            sort($userAnswerArray);
            
            return $correctAnswers === $userAnswerArray;
        } else {
            // Single answer comparison
            return (int)$userAnswer === (int)$this->correct_answer_index;
        }
    }

    /**
     * Relationship dengan AlatTes
     */
    public function alatTes()
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    /**
 * Relationship to RMIB Item
 */
public function rmibItem()
{
    return $this->belongsTo(RmibItem::class);
}
    /**
     * Get options as array
     */
    public function getOptionsArray()
    {
        if (is_string($this->options)) {
            return json_decode($this->options, true) ?? [];
        }
        return $this->options ?? [];
    }

    /**
     * Get display type (friendly name)
     */
    public function getDisplayType()
    {
        $types = [
            'PILIHAN_GANDA' => 'Pilihan Ganda (1 Jawaban)',
            'PILIHAN_GANDA_KOMPLEKS' => 'Pilihan Ganda Kompleks (Banyak Jawaban)',
            'ESSAY' => 'Esai',
            'HAFALAN' => 'Hafalan',
            'PAPIKOSTICK' => 'PAPI Kostick',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get correct answer display
     */
    public function getCorrectAnswerDisplay()
    {
        if ($this->hasMultipleCorrectAnswers()) {
            $answers = $this->getCorrectAnswersArray();
            $letters = array_map(function($idx) {
                return chr(65 + $idx);
            }, $answers);
            return implode(', ', $letters);
        } else {
            $idx = $this->correct_answer_index;
            return chr(65 + $idx);
        }
    }
}