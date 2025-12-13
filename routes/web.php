<?php

use Illuminate\Support\Facades\Route;

// User-side Controllers
use App\Http\Controllers\TestAccessController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserTestController;
use App\Http\Controllers\PapiTestController;
use App\Http\Controllers\UserPauliController;
use App\Http\Controllers\ViolationController;

// Admin-side Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InterpretationRuleController;
use App\Http\Controllers\Admin\JenjangController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\PapiQuestionController;
use App\Http\Controllers\Admin\RmibQuestionController;
use App\Http\Controllers\Admin\TestCategoryController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\AlatTesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivationCodeController;
use App\Http\Controllers\Admin\ReportController;

// Auth & Profile Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfileController;

// Middleware
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| ✅ CSRF TOKEN REFRESH ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token(),
        'timestamp' => now()->timestamp
    ]);
})->name('csrf-token');

/*
|--------------------------------------------------------------------------
| ALUR PESERTA BARU
|--------------------------------------------------------------------------
*/
Route::get('/', [TestAccessController::class, 'showLoginForm'])->name('login');
Route::post('/', [TestAccessController::class, 'login'])->name('login.process');
Route::post('/logout', [TestAccessController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ALUR DATA DIRI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [UserDataController::class, 'edit'])->name('user.data.edit');
    Route::post('/profile/update', [UserDataController::class, 'update'])->name('user.data.update');
});

/*
|--------------------------------------------------------------------------
| ✅ ALUR TES (FIXED - Menambahkan Route Constraints)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // PENTING: Route spesifik harus di atas route dengan parameter dinamis

    // 1. Halaman daftar test yang tersedia
    Route::get('/tests/start', [UserTestController::class, 'start'])
        ->name('tests.start');

    // 2. Halaman untuk memilih test tertentu (harus sebelum route dinamis)
    Route::get('/tests/start/{test}', [UserTestController::class, 'startTest'])
        ->name('tests.start.with')
        ->where('test', '[0-9]+'); // Tambahkan constraint: hanya angka

    // 2.5. ✅ Dashboard Modul - Menampilkan list alat tes
    Route::get('/tests/{test}/dashboard', [UserTestController::class, 'showDashboard'])
        ->name('tests.dashboard')
        ->where('test', '[0-9]+');

    // 3. Module finish (harus sebelum route dinamis)
    Route::get('/tests/module-finish/{test}', [UserTestController::class, 'finishModule'])
        ->name('tests.module.finish')
        ->where('test', '[0-9]+');
    
    // ✅ HALAMAN PERSIAPAN & PETUNJUK
    Route::get('/tests/{test}/{alat_tes}/preparation', [UserTestController::class, 'showPreparation'])
        ->name('tests.preparation')
        ->where(['test' => '[0-9]+', 'alat_tes' => '[0-9]+']);
    
    Route::get('/tests/{test}/{alat_tes}/instructions', [UserTestController::class, 'showInstructions'])
        ->name('tests.instructions')
        ->where(['test' => '[0-9]+', 'alat_tes' => '[0-9]+']);
    
    Route::match(['GET', 'POST'], '/tests/{test}/{alat_tes}/start-test', [UserTestController::class, 'startAlatTes'])
        ->name('tests.alat.start')
        ->where(['test' => '[0-9]+', 'alat_tes' => '[0-9]+']);
    
    // Rute untuk menampilkan soal umum
    Route::get('/tests/{test}/{alat_tes}/question/{number}', [UserTestController::class, 'showQuestion'])
        ->name('tests.question')
        ->where(['test' => '[0-9]+', 'alat_tes' => '[0-9]+', 'number' => '[0-9]+']);
    
    // Rute untuk menyimpan jawaban umum
    Route::post('/tests/{test}/{alat_tes}/answer/{number}', [UserTestController::class, 'saveAnswer'])
        ->name('tests.answer')
        ->where(['test' => '[0-9]+', 'alat_tes' => '[0-9]+', 'number' => '[0-9]+']);

    // Rute Tes Khusus (submit endpoints)
    Route::post('/tests/{test}/alat/{alat_tes}/papi/submit', [PapiTestController::class, 'submitTest'])
    ->middleware('auth')
    ->name('papi.submit');
    
    Route::post('/tests/{test}/pauli/submit', [UserPauliController::class, 'submitTest'])
        ->name('pauli.submit')
        ->where('test', '[0-9]+');
    
    // Route LEGACY - Taruh di paling bawah
    Route::get('/tests/{test}', [UserTestController::class, 'show'])
        ->name('tests.show')
        ->where('test', '[0-9]+');
    
    Route::post('/tests/{test}/submit', [UserTestController::class, 'store'])
        ->name('tests.store')
        ->where('test', '[0-9]+');
    
    // Rute hasil tes
    Route::get('/results/{testResult}', [UserTestController::class, 'result'])
        ->name('tests.result')
        ->where('testResult', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| ✅ ANTI-CHEATING API ROUTES (USER SIDE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Log violation dari user saat mengerjakan test
    Route::post('/api/log-violation', [ViolationController::class, 'logViolation'])
        ->name('api.log-violation');

    // Heartbeat untuk tracking user masih aktif
    Route::post('/api/test-heartbeat', [ViolationController::class, 'testHeartbeat'])
        ->name('api.test-heartbeat');

    // Halaman test terminated
    Route::get('/test/terminated', [ViolationController::class, 'terminated'])
        ->name('test.terminated');
});

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('login.admin');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');
Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.admin');

/*
|--------------------------------------------------------------------------
| PANEL ADMIN (MODIFIED FOR TEST ORDER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Admin
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        // ==========================================================
        // KODE AKTIVASI
        // ==========================================================
        Route::resource('activation-codes', ActivationCodeController::class)
            ->only(['index', 'store', 'show', 'destroy'])
            ->names([
                'index' => 'codes.index',
                'store' => 'codes.store',
                'show' => 'codes.show',
                'destroy' => 'codes.destroy',
            ]);
        Route::get('activation-codes/{code}/export', [ActivationCodeController::class, 'exportBatch'])->name('codes.export');

        // ==========================================================
        // MANAJEMEN MASTER DATA
        // ==========================================================
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('categories', TestCategoryController::class);
        Route::resource('jenjangs', JenjangController::class)->except(['show']);

        // ==========================================================
        // ✅ MANAJEMEN TES (FINAL FIX: Urutan Route Diubah)
        // ==========================================================

        // TAHAP 1: Menampilkan form create (HARUS DITARUH PALING ATAS untuk menghindari 404)
        Route::get('tests/create', [TestController::class, 'create'])->name('tests.create');

        // TAHAP 1.5: Menyimpan data step 1 ke session (Memperbaiki RouteNotFoundException)
        Route::post('tests/store/step-one', [TestController::class, 'storeStepOne'])->name('tests.store.step.one');

        // TAHAP 2: Menampilkan form order alat tes
        Route::get('tests/create/order', [TestController::class, 'createOrder'])->name('tests.create.order');

        // TAHAP 2.5: Menyimpan data final dari session ke DB
        Route::post('tests/store/final', [TestController::class, 'storeFinal'])->name('tests.store.final');
        
        // Resource routes standar (index, show, edit, update, destroy)
        // Mengecualikan 'create' dan 'store' karena sudah dibuat secara eksplisit di atas
        Route::resource('tests', TestController::class)->except(['create', 'store'])->names('tests');

        // Rute untuk mengelola urutan alat tes pada tes yang sudah ada (editOrder/updateOrder)
        Route::get('tests/{test}/order', [TestController::class, 'editOrder'])->name('tests.order.edit');
        Route::put('tests/{test}/order', [TestController::class, 'updateOrder'])->name('tests.order.update');

        Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
        Route::get('tests/{test}/export', [TestController::class, 'export'])->name('tests.export');
        Route::resource('tests.rules', InterpretationRuleController::class)->except(['show'])->names('tests.rules');

        // ==========================================================
        // ALAT TES
        // ==========================================================
        Route::resource('alat-tes', AlatTesController::class)->names('alat-tes');

        // ==========================================================
        // ✅ QUESTIONS MANAGEMENT
        // ==========================================================

        // Main Questions Index (Shows all types)
        Route::get('alat-tes/{alat_te}/questions', [QuestionController::class, 'index'])
            ->name('alat-tes.questions.index');

        // -------------------- GENERAL QUESTIONS --------------------
        Route::get('alat-tes/{alat_te}/questions/create', [QuestionController::class, 'create'])
            ->name('alat-tes.questions.create');

        Route::post('alat-tes/{alat_te}/questions', [QuestionController::class, 'store'])
            ->name('alat-tes.questions.store');

        Route::get('alat-tes/{alat_te}/questions/{question}/edit', [QuestionController::class, 'edit'])
            ->name('alat-tes.questions.edit');

        Route::put('alat-tes/{alat_te}/questions/{question}', [QuestionController::class, 'update'])
            ->name('alat-tes.questions.update');

        Route::patch('alat-tes/{alat_te}/questions/{question}', [QuestionController::class, 'update'])
            ->name('alat-tes.questions.patch');

        Route::delete('alat-tes/{alat_te}/questions/{question}', [QuestionController::class, 'destroy'])
            ->name('alat-tes.questions.destroy');

        // -------------------- PAPI KOSTICK QUESTIONS --------------------
        Route::get('alat-tes/{alat_te}/questions/papi/create', [PapiQuestionController::class, 'create'])
            ->name('alat-tes.questions.papi.create');

        Route::post('alat-tes/{alat_te}/questions/papi', [PapiQuestionController::class, 'store'])
            ->name('alat-tes.questions.papi.store');

        Route::get('alat-tes/{alat_te}/questions/papi/{question}/edit', [PapiQuestionController::class, 'edit'])
            ->name('alat-tes.questions.papi.edit');

        Route::put('alat-tes/{alat_te}/questions/papi/{question}', [PapiQuestionController::class, 'update'])
            ->name('alat-tes.questions.papi.update');

        Route::delete('alat-tes/{alat_te}/questions/papi/{question}', [PapiQuestionController::class, 'destroy'])
            ->name('alat-tes.questions.papi.destroy');

        // -------------------- ✅ RMIB QUESTIONS (FIXED) --------------------
        Route::get('alat-tes/{alatTesId}/questions/rmib/create', [RmibQuestionController::class, 'create'])
            ->name('alat-tes.questions.rmib.create');

        Route::post('alat-tes/{alatTesId}/questions/rmib', [RmibQuestionController::class, 'store'])
            ->name('alat-tes.questions.rmib.store');

        Route::get('alat-tes/{alatTesId}/questions/rmib/{questionId}/edit', [RmibQuestionController::class, 'edit'])
            ->name('alat-tes.questions.rmib.edit');

        Route::put('alat-tes/{alatTesId}/questions/rmib/{questionId}', [RmibQuestionController::class, 'update'])
            ->name('alat-tes.questions.rmib.update');

        Route::delete('alat-tes/{alatTesId}/questions/rmib/{questionId}', [RmibQuestionController::class, 'destroy'])
            ->name('alat-tes.questions.rmib.destroy');

        // -------------------- SHARED FEATURES --------------------
        Route::post('alat-tes/{alat_te}/questions/import', [QuestionController::class, 'import'])
            ->name('alat-tes.questions.import');
        Route::get('alat-tes/questions/download-template', [QuestionController::class, 'downloadTemplate'])
            ->name('alat-tes.questions.template');

        Route::post('alat-tes/{alat_te}/questions/{question}/options', [OptionController::class, 'store'])
            ->name('alat-tes.questions.options.store');
        Route::delete('alat-tes/{alat_te}/options/{option}', [OptionController::class, 'destroy'])
            ->name('alat-tes.options.destroy');

        Route::post('alat-tes/{alat_te}/example-questions', [QuestionController::class, 'storeExample'])
            ->name('alat-tes.example-questions.store');
        Route::delete('alat-tes/{alat_te}/example-questions/{example}', [QuestionController::class, 'destroyExample'])
            ->name('alat-tes.example-questions.destroy');

        // ==========================================================
        // USER & PESERTA MANAGEMENT
        // ==========================================================
        Route::resource('users', UserController::class)->except(['create', 'store']);

        Route::get('peserta', [PesertaController::class, 'index'])->name('peserta.index');
        Route::get('peserta/{user}', [PesertaController::class, 'show'])->name('peserta.show');
        Route::delete('peserta/{user}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

        // ==========================================================
        // ✅ VIOLATIONS MONITORING (ADMIN)
        // ==========================================================

        // Dashboard violations - Lihat semua pelanggaran
        Route::get('violations', [ViolationController::class, 'adminIndex'])
            ->name('violations.index');

        // Detail violations per user
        Route::get('violations/user/{userId}', [ViolationController::class, 'adminUserViolations'])
            ->name('violations.user');

        // Delete violation (jika false positive)
        Route::delete('violations/{id}', [ViolationController::class, 'adminDelete'])
            ->name('violations.delete');

        // Export violations report
        Route::get('violations/export', [ViolationController::class, 'export'])
            ->name('violations.export');

        // ==========================================================
        // ✅ LAPORAN (REPORTS)
        // ==========================================================
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        // Rute Show (menampilkan daftar peserta di dalam Batch)
        Route::get('reports/{code}', [ReportController::class, 'show'])->name('reports.show');
        // Rute PDF (mengunduh hasil tes berdasarkan ID TestResult)
        Route::get('reports/pdf/{testResult}', [ReportController::class, 'generatePdfReport'])->name('reports.pdf');
        
        // ==========================================================
        // API CHEATING DETECTION (LEGACY - Untuk backward compatibility)
        // ==========================================================
        Route::post('/api/cheating/log', [App\Http\Controllers\Api\CheatingDetectionController::class, 'logViolation'])
            ->name('api.cheating.log');
        Route::get('/api/cheating/status', [App\Http\Controllers\Api\CheatingDetectionController::class, 'checkStatus'])
            ->name('api.cheating.status');
    });