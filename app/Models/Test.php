<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Test extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'test_code',
        'test_category_id',
        'jenjang_id',
        'title',
        'description',
        'duration_minutes',
        'is_published',
        'is_template',
        'available_from',
        'available_to',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($test) {
            if (empty($test->test_code)) {
                $test->test_code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Membuat kode unik yang belum ada di database.
     */
    private static function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (self::where('test_code', $code)->exists());

        return $code;
    }

    // --- Relasi ---
    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function interpretationRules(): HasMany
    {
        return $this->hasMany(InterpretationRule::class)->orderBy('min_score', 'asc');
    }

    /**
     * Relasi ke model ActivationCode.
     * Satu tes bisa memiliki banyak kode aktivasi.
     */
    public function activationCodes(): HasMany
    {
        return $this->hasMany(ActivationCode::class);
    }
}
