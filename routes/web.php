<?php

use Illuminate\Support\Facades\Route;
// User-side Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserTestController;
// Admin-side Controllers
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestCategoryController;
use App\Http\Controllers\Admin\TestController;
// Middleware
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// =====================================================================
// == GRUP RUTE UNTUK PENGGUNA TERAUTENTIKASI (USER BIASA & ADMIN) ==
// =====================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (menampilkan daftar tes)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Alur Pengerjaan Tes oleh Pengguna
    Route::get('tests/{test}', [UserTestController::class, 'show'])->name('tests.show');
    Route::post('tests/{test}/submit', [UserTestController::class, 'store'])->name('tests.store');
    Route::get('results/{testResult}', [UserTestController::class, 'result'])->name('tests.result');
});

// =====================================================================
// == GRUP RUTE KHUSUS UNTUK PANEL ADMIN ==
// =====================================================================
Route::middleware(['auth', IsAdmin::class])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
        
    Route::resource('categories', TestCategoryController::class);
    Route::resource('tests', TestController::class);
    Route::resource('tests.questions', QuestionController::class)->shallow();
    Route::post('questions/{question}/options', [OptionController::class, 'store'])->name('questions.options.store');
    Route::delete('options/{option}', [OptionController::class, 'destroy'])->name('options.destroy');
});


// Rute Autentikasi Bawaan Laravel Breeze
require __DIR__.'/auth.php';