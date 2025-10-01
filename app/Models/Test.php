<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'client_id', 'test_code', 'test_category_id', 'jenjang_id',
        'title', 'description', 'duration_minutes', 'is_published',
        'is_template', 'available_from', 'available_to',
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
     * Membuat kode unik.
     */
    private static function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (self::where('test_code', $code)->exists());

        return $code;
    }

    // --- RELASI TAMBAHAN UNTUK FIX ERROR BadMethodCallException ---

    /**
     * Relasi: Sebuah Tes memiliki banyak Questions.
     * Metode ini harus ada karena dipanggil oleh TestController.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(): HasMany
    {
        // Asumsi model Question ada
        return $this->hasMany(Question::class);
    }

    /**
     * Relasi: Sebuah Tes memiliki banyak InterpretationRule.
     * Metode ini juga sering dipanggil bersamaan dengan questions().
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function interpretationRules(): HasMany
    {
        // Asumsi model InterpretationRule ada
        return $this->hasMany(InterpretationRule::class)->orderBy('min_score', 'asc');
    }

    // --- RELASI BARU (Relasi yang sudah ada di Model Anda) ---
    /**
     * Relasi Many-to-Many: Satu Modul (Test) bisa terdiri dari banyak Alat Tes.
     */
    public function alatTes(): BelongsToMany
    {
        // Menghubungkan ke model AlatTes melalui tabel pivot 'modul_alat_tes'
        return $this->belongsToMany(AlatTes::class, 'modul_alat_tes');
    }

    // --- Relasi Lainnya (Tidak Berubah) ---
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
    
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
