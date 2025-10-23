<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PapiResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'participant_name',
        'participant_number',
        'test_date',
        'check_date',
        'answers',
        'interpretation',
        'completed_at',

        // 20 Aspek Skor PAPI
        'G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 
        'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W', 
    ];

    protected $casts = [
        'answers' => 'array',
        'test_date' => 'date',
        'check_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mengambil skor dari kolom spesifik.
     */
    public function getScore($dimension)
    {
        // Langsung akses kolom database
        return $this->$dimension ?? 0;
    }

    /**
     * Mengambil semua skor dalam urutan PAPI standar.
     */
    public function getOrderedScores()
    {
        $order = ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W']; 
        $orderedScores = [];
        
        foreach ($order as $dimension) {
            $orderedScores[$dimension] = $this->getScore($dimension);
        }
        
        return $orderedScores;
    }

    // --- Accessor dan Helper lainnya (Seperti getHighestDimensions, getProfileType, dll. tetap sama dan akan berfungsi setelah penyesuaian getScore) ---
    // Pastikan Anda memperbarui logika di dalam getHighestDimensions dan lainnya untuk menggunakan $this->getOrderedScores()
    public function getHighestDimensions($count = 5)
    {
        // Menggunakan skor dari helper getOrderedScores()
        $scores = $this->getOrderedScores(); 
        arsort($scores);
        return array_slice($scores, 0, $count, true);
    }

    /**
     * Get lowest dimensions
     */
    public function getLowestDimensions($count = 5)
    {
        $scores = $this->scores ?? [];
        asort($scores);
        
        return array_slice($scores, 0, $count, true);
    }

    /**
     * Get dimension category (Low, Average, High)
     */
    public function getDimensionCategory($dimension)
    {
        $score = $this->getScore($dimension);
        
        if ($score <= 3) {
            return 'Low';
        } elseif ($score <= 6) {
            return 'Average';
        } else {
            return 'High';
        }
    }

    /**
     * Get percentage score
     */
    public function getScorePercentage($dimension)
    {
        $score = $this->getScore($dimension);
        $maxScore = 9; // Maximum score per dimension in PAPI
        
        return round(($score / $maxScore) * 100, 2);
    }

    /**
     * Calculate completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $totalQuestions = 90;
        $answeredQuestions = count($this->answers ?? []);
        
        return round(($answeredQuestions / $totalQuestions) * 100, 2);
    }

    /**
     * Check if test is complete
     */
    public function isComplete()
    {
        return count($this->answers ?? []) === 90;
    }

    /**
     * Get answer for specific question
     */
    public function getAnswer($questionNumber)
    {
        return $this->answers[$questionNumber] ?? null;
    }

    /**
     * Get all answers with question details
     */
    public function getAnswersWithQuestions()
    {
        $questions = PapiQuestion::ordered()->get();
        $result = [];
        
        foreach ($questions as $question) {
            $result[] = [
                'question' => $question,
                'answer' => $this->getAnswer($question->number),
                'selected_dimension' => $this->getAnswer($question->number) === 'a' 
                    ? $question->dimension_a 
                    : $question->dimension_b,
            ];
        }
        
        return $result;
    }

    /**
     * Generate interpretation based on scores
     */
    public function generateInterpretation()
    {
        $highest = $this->getHighestDimensions(3);
        $dimensions = PapiQuestion::getDimensions();
        
        $interpretation = "Berdasarkan hasil tes PAPI Kostick:\n\n";
        $interpretation .= "Dimensi Tertinggi:\n";
        
        foreach ($highest as $dim => $score) {
            $interpretation .= "- {$dim}: {$dimensions[$dim]} (Skor: {$score})\n";
        }
        
        return $interpretation;
    }

    /**
     * Export to PDF
     */
    public function exportToPdf()
    {
        // TODO: Implement PDF export using dompdf or similar
        return false;
    }

    /**
     * Scope untuk filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('test_date', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter by participant name
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('participant_name', 'like', "%{$name}%");
    }

    /**
     * Get PAPI profile type
     */
    public function getProfileType()
    {
        $highest = $this->getHighestDimensions(3);
        $dimensions = array_keys($highest);
        
        // Leadership profile
        if (in_array('L', $dimensions) && in_array('P', $dimensions)) {
            return 'Leadership';
        }
        
        // Social profile
        if (in_array('I', $dimensions) && in_array('T', $dimensions)) {
            return 'Social';
        }
        
        // Detail-oriented profile
        if (in_array('O', $dimensions) && in_array('D', $dimensions)) {
            return 'Detail-Oriented';
        }
        
        // Achievement profile
        if (in_array('A', $dimensions) && in_array('N', $dimensions)) {
            return 'Achievement-Oriented';
        }
        
        return 'General';
    }

    /**
     * Calculate work style based on dimensions
     */
    public function getWorkStyle()
    {
        $scores = $this->scores;
        
        $leadership = ($scores['L'] ?? 0) + ($scores['P'] ?? 0);
        $teamwork = ($scores['I'] ?? 0) + ($scores['T'] ?? 0);
        $independent = ($scores['F'] ?? 0) + ($scores['C'] ?? 0);
        
        if ($leadership > $teamwork && $leadership > $independent) {
            return 'Leadership-Oriented';
        } elseif ($teamwork > $independent) {
            return 'Team-Oriented';
        } else {
            return 'Independent Worker';
        }
    }
}