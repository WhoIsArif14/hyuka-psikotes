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
use App\Http\Controllers\Admin\TestCreationWizardController;
use App\Http\Controllers\Admin\UserController;
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

// Langkah 1: MENAMPILKAN form untuk memasukkan KODE TES.
// Karena ini untuk MENAMPILKAN halaman, metodenya HARUS Route::get().
// Inilah sumber error Anda.
Route::get('/', [TestAccessController::class, 'showCodeForm'])->name('login');

// Route ini untuk MEMPROSES form yang dikirim dari halaman di atas, jadi metodenya Route::post().
Route::post('/login', [TestAccessController::class, 'processCode'])->name('test-code.process');

// Langkah 2: MENAMPILKAN form untuk memasukkan NAMA, jadi metodenya Route::get().
Route::get('/enter-name', [TestAccessController::class, 'showNameForm'])->name('test-code.name');

// Route ini untuk MEMPROSES form nama, jadi metodenya Route::post().
Route::post('/enter-name', [TestAccessController::class, 'startTest'])->name('test-code.start');

// Langkah 3: Mengerjakan dan menyimpan tes
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

        // Wizard Pembuatan Tes
        Route::prefix('create-test')->name('wizard.')->group(function() {
            Route::get('step-1', [TestCreationWizardController::class, 'step1_category'])->name('step1');
            Route::post('step-1', [TestCreationWizardController::class, 'postStep1_category'])->name('post_step1');
            Route::get('step-2', [TestCreationWizardController::class, 'step2_template'])->name('step2');
            Route::post('step-2', [TestCreationWizardController::class, 'postStep2_template'])->name('post_step2');
            Route::get('{test}/step-3', [TestCreationWizardController::class, 'step3_schedule'])->name('step3');
            Route::post('{test}/step-3', [TestCreationWizardController::class, 'postStep3_schedule'])->name('post_step3');
        });
        
        // Manajemen Umum
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('categories', TestCategoryController::class);
        Route::resource('tests', TestController::class);
        Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
        Route::get('tests/{test}/export', [TestController::class, 'export'])->name('tests.export');
        Route::resource('tests.questions', QuestionController::class)->shallow();
        Route::post('questions/{question}/options', [OptionController::class, 'store'])->name('questions.options.store');
        Route::delete('options/{option}', [OptionController::class, 'destroy'])->name('options.destroy');
        Route::resource('tests.rules', InterpretationRuleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::resource('jenjangs', JenjangController::class)->except(['show']);
        Route::get('peserta', [PesertaController::class, 'index'])->name('peserta.index');
        Route::get('peserta/{user}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::delete('peserta/{user}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
});
