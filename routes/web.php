<?php

use Illuminate\Support\Facades\Route;

// User-side Controllers
use App\Http\Controllers\TestAccessController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserTestController;
use App\Http\Controllers\PapiTestController;

// Admin-side Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InterpretationRuleController;
use App\Http\Controllers\Admin\JenjangController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestCategoryController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\AlatTesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivationCodeController;

// Auth & Profile Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfileController;

// Middleware
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| ALUR PESERTA BARU (LOGIN DENGAN KODE AKTIVASI PESERTA)
|--------------------------------------------------------------------------
*/

// Rute Tampil Form (GET /)
Route::get('/', [TestAccessController::class, 'showLoginForm'])->name('login');

// Rute Proses Login (POST /)
Route::post('/', [TestAccessController::class, 'login'])->name('login.process');

// Rute Logout Peserta
Route::post('/logout', [TestAccessController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| ALUR DATA DIRI DAN TES (DILINDUNGI OLEH AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 1. Pengisian Data Diri
    Route::get('/profile/edit', [UserDataController::class, 'edit'])->name('user.data.edit');
    Route::post('/profile/update', [UserDataController::class, 'update'])->name('user.data.update');

    // 2. Start Test
    Route::get('/tests/start', [UserTestController::class, 'start'])->name('tests.start');
    Route::get('/tests/start/{test}', [UserTestController::class, 'startTest'])->name('tests.start.with');

    // 3. Sistem 1 Soal per Halaman
    Route::get('/tests/{test}/question/{number}', [UserTestController::class, 'showQuestion'])
        ->name('tests.question');
    Route::post('/tests/{test}/question/{number}', [UserTestController::class, 'saveAnswer'])
        ->name('tests.answer');

    // 4. Tes Umum
    Route::get('tests/{test}', [UserTestController::class, 'show'])->name('tests.show');
    Route::post('tests/{test}/submit', [UserTestController::class, 'store'])->name('tests.store');
    Route::get('results/{testResult}', [UserTestController::class, 'result'])->name('tests.result');

    // 5. Tes PAPI Kostick
    Route::post('tests/{test}/papi/submit', [PapiTestController::class, 'submitTest'])->name('papi.submit');
});


/*
|--------------------------------------------------------------------------
| LOGIN ADMIN (EMAIL & PASSWORD)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('login.admin');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.admin');


/*
|--------------------------------------------------------------------------
| PANEL ADMIN (DILINDUNGI OLEH MIDDLEWARE IsAdmin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Admin
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        // --- KODE AKTIVASI ---
        Route::resource('activation-codes', ActivationCodeController::class)
            ->only(['index', 'store', 'show', 'destroy'])
            ->names([
                'index' => 'codes.index',
                'store' => 'codes.store',
                'show' => 'codes.show',
                'destroy' => 'codes.destroy',
            ]);
        Route::get('activation-codes/{code}/export', [ActivationCodeController::class, 'exportBatch'])
            ->name('codes.export');

        // ==========================================================
        // MANAJEMEN TEST & ALAT TES
        // ==========================================================
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('categories', TestCategoryController::class);
        Route::resource('jenjangs', JenjangController::class)->except(['show']);

        // 1️⃣ Modul Tests
        Route::resource('tests', TestController::class)->names('tests');
        Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
        Route::get('tests/{test}/export', [TestController::class, 'export'])->name('tests.export');
        Route::resource('tests.rules', InterpretationRuleController::class)->except(['show'])->names('tests.rules');

        // 2️⃣ Alat Tes
        Route::resource('alat-tes', AlatTesController::class)->names('alat-tes');

        // ==========================================================
        // RUTE QUESTIONS (PERBAIKAN LENGKAP)
        // ==========================================================

        // INDEX, CREATE, STORE
        Route::get('alat-tes/{alat_te}/questions', [QuestionController::class, 'index'])
            ->name('alat-tes.questions.index');
        Route::get('alat-tes/{alat_te}/questions/create', [QuestionController::class, 'create'])
            ->name('alat-tes.questions.create');
        Route::post('alat-tes/{alat_te}/questions', [QuestionController::class, 'store'])
            ->name('alat-tes.questions.store');

        // SHOW
        Route::get('questions/{question}', [QuestionController::class, 'show'])
            ->name('questions.show');

        // EDIT (sudah benar di view)
        Route::get('alat-tes/{alat_te}/questions/{question}/edit', [QuestionController::class, 'edit'])
            ->name('alat-tes.questions.edit');

        // ✅ UPDATE untuk PAPI
        Route::put('alat-tes/{alat_te}/questions/{papi_question}/update-papi', [QuestionController::class, 'updatePapi'])
            ->name('alat-tes.questions.update_papi');

        // ✅ UPDATE untuk Soal Umum (PENTING — Tambahan Baru)
        Route::put('alat-tes/{alat_te}/questions/{question}', [QuestionController::class, 'update'])
            ->name('alat-tes.questions.update');

        // DELETE (hapus soal)
        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])
            ->name('questions.destroy');

        // Import / Template / Options
        Route::post('questions/{question}/import', [QuestionController::class, 'import'])->name('questions.import');
        Route::get('questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('questions.template');
        Route::post('questions/{question}/options', [OptionController::class, 'store'])->name('questions.options.store');
        Route::delete('options/{option}', [OptionController::class, 'destroy'])->name('options.destroy');

        Route::post('alat-tes/{alat_te}/example-questions', [QuestionController::class, 'storeExample'])
    ->name('alat-tes.example-questions.store');
Route::delete('alat-tes/{alat_te}/example-questions/{example}', [QuestionController::class, 'destroyExample'])
    ->name('alat-tes.example-questions.destroy');
        // User & Peserta
        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::get('peserta', [PesertaController::class, 'index'])->name('peserta.index');
        Route::get('peserta/{user}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::delete('peserta/{user}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

        Route::post('/api/cheating/log', [App\Http\Controllers\Api\CheatingDetectionController::class, 'logViolation'])
            ->name('api.cheating.log');
        Route::get('/api/cheating/status', [App\Http\Controllers\Api\CheatingDetectionController::class, 'checkStatus'])
            ->name('api.cheating.status');
    });
