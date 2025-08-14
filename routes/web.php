<?php

use Illuminate\Support\Facades\Route;
// User-side Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserTestController;
// Admin-side Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\InterpretationRuleController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestCategoryController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\UserController;
// Middleware
use App\Http\Middleware\IsAdmin;

// Rute Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// Grup Rute untuk Pengguna Terautentikasi
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('tests/{test}', [UserTestController::class, 'show'])->name('tests.show');
    Route::post('tests/{test}/submit', [UserTestController::class, 'store'])->name('tests.store');
    Route::get('results/{testResult}', [UserTestController::class, 'result'])->name('tests.result');
    Route::get('my-results', [DashboardController::class, 'history'])->name('tests.history');
});

// Grup Rute Khusus untuk Panel Admin
Route::middleware(['auth', IsAdmin::class])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', TestCategoryController::class);
        Route::resource('tests', TestController::class);
        Route::get('tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
        
        // --- ROUTE YANG PERLU DITAMBAHKAN ADA DI SINI ---
        Route::get('tests/{test}/export', [TestController::class, 'export'])->name('tests.export');
        
        Route::resource('tests.questions', QuestionController::class)->shallow();
        Route::post('questions/{question}/options', [OptionController::class, 'store'])->name('questions.options.store');
        Route::delete('options/{option}', [OptionController::class, 'destroy'])->name('options.destroy');
        Route::resource('tests.rules', InterpretationRuleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['create', 'store']);
});

// Rute Autentikasi Bawaan Laravel Breeze
require __DIR__.'/auth.php';