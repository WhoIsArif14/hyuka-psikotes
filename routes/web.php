<?php

use Illuminate\Support\Facades\Route;
// User-side Controllers
use App\Http\Controllers\TestAccessController;
use App\Http\Controllers\UserTestController;
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
use App\Http\Controllers\Admin\alatTesController;
use App\Http\Controllers\Admin\TestCreationWizardController;
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
| ALUR PESERTA (TANPA LOGIN AKUN)
|--------------------------------------------------------------------------
*/

Route::get('/', [TestAccessController::class, 'showCodeForm'])->name('login');
Route::post('/', [TestAccessController::class, 'processCode'])->name('test-code.process');
Route::get('/enter-name', [TestAccessController::class, 'showNameForm'])->name('test-code.name');
Route::post('/enter-name', [TestAccessController::class, 'startTest'])->name('test-code.start');
Route::get('tests/{test}', [UserTestController::class, 'show'])->name('tests.show');
Route::post('tests/{test}/submit', [UserTestController::class, 'store'])->name('tests.store');
Route::get('results/{testResult}', [UserTestController::class, 'result'])->name('tests.result');


/*
|--------------------------------------------------------------------------
| ALUR LOGIN ADMIN (EMAIL & PASSWORD)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('login.admin');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.admin');


/*
|--------------------------------------------------------------------------
| PANEL ADMIN (DILINDUNGI)
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

        // --- RUTE KODE AKTIVASI (DIPERBAIKI) ---
        Route::resource('activation-codes', ActivationCodeController::class)
            ->only(['index', 'store', 'show', 'destroy'])
            ->names([
                'index' => 'codes.index',
                'store' => 'codes.store',
                'show' => 'codes.show',
                'destroy' => 'codes.destroy',
            ]);

        // Wizard Pembuatan Tes
        Route::prefix('create-test')->name('wizard.')->group(function () {
            Route::get('step-1', [TestCreationWizardController::class, 'step1_category'])->name('step1');
            Route::post('step-1', [TestCreationWizardController::class, 'postStep1_category'])->name('post_step1');
            Route::get('step-2', [TestCreationWizardController::class, 'step2_template'])->name('step2');
            Route::post('step-2', [TestCreationWizardController::class, 'postStep2_template'])->name('post_step2');
            Route::get('{test}/step-3', [TestCreationWizardController::class, 'step3_schedule'])->name('step3');
            Route::post('{test}/step-3', [TestCreationWizardController::class, 'postStep3_schedule'])->name('post_step3');
        });

        // ==========================================================
        // MANAJEMEN UMUM & PEMISAHAN TESTS/ALAT-TES
        // ==========================================================
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('categories', TestCategoryController::class);
        Route::resource('jenjangs', JenjangController::class)->except(['show']);

        // 1. RESOURCE TESTS (MODUL) - Mengarah ke TestController
        Route::resource('tests', TestController::class)->names('tests');

        // Rute terkait Modul/Tests
        Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
        Route::get('tests/{test}/export', [TestController::class, 'export'])->name('tests.export');
        Route::resource('tests.rules', InterpretationRuleController::class)->except(['show'])->names('tests.rules');

        // 2. RESOURCE ALAT-TES - Mengarah ke alatTesController
        Route::resource('alat-tes', alatTesController::class)->names('alat-tes');

        // ==========================================================
        // RUTE QUESTIONS
        // ==========================================================
        Route::get('alat-tes/{alat_te}/questions', [QuestionController::class, 'index'])
            ->name('alat-tes.questions.index');

        Route::get('alat-tes/{alat_te}/questions/create', [QuestionController::class, 'create'])
            ->name('alat-tes.questions.create');

        Route::post('alat-tes/{alat_te}/questions', [QuestionController::class, 'store'])
            ->name('alat-tes.questions.store');

        Route::get('questions/{question}', [QuestionController::class, 'show'])
            ->name('questions.show');

        Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])
            ->name('questions.edit');

        Route::put('questions/{question}', [QuestionController::class, 'update'])
            ->name('questions.update');

        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])
            ->name('questions.destroy');

        // Rute terkait Question/Options
        Route::post('questions/{question}/import', [QuestionController::class, 'import'])->name('questions.import');
        Route::get('questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('questions.template');
        Route::post('questions/{question}/options', [OptionController::class, 'store'])->name('questions.options.store');
        Route::delete('options/{option}', [OptionController::class, 'destroy'])->name('options.destroy');

        // Rute Manajemen Pengguna dan Peserta
        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::get('peserta', [PesertaController::class, 'index'])->name('peserta.index');
        Route::get('peserta/{user}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::delete('peserta/{user}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
    });
