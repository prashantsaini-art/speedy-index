<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ApiLogController;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Tasks
    Route::resource('tasks', TaskController::class);

    // Fixed Line 15: Corrected method name and syntax
    Route::get('/tasks/{task}/download-report', [TaskController::class, 'downloadReport'])
        ->name('tasks.download-report');

    // API Logs
    Route::get('/api-logs', [ApiLogController::class, 'index'])
        ->name('api-logs.index');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
