<?php

use App\Http\Controllers\assistanai\DocumentController;
use App\Http\Controllers\assistanai\DocumentItemsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/assistantai', [DocumentController::class, 'index'])->name('assistantai.pages.index');

    Route::get('/assistantai/create', [DocumentController::class, 'create'])->name('assistantai.pages.create');
    Route::post('/assistantai', [DocumentController::class, 'store'])->name('assistantai.pages.store');

    Route::get('/assistantai/{id}/edit', [DocumentController::class, 'edit'])->name('assistantai.pages.edit');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/cek', function () {
    return view('assistantai.pages.create');
});

require __DIR__ . '/auth.php';
