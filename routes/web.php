<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\LeaveController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // TIME LOGS
    Route::get('/time-logs', [TimeLogController::class, 'index'])->name('time_logs.index');
    Route::get('/time-logs/create', [TimeLogController::class, 'create'])->name('time_logs.create');
    Route::post('/time-logs', [TimeLogController::class, 'store'])->name('time_logs.store');
    Route::get('/time-logs/{timeLog}/edit', [TimeLogController::class, 'edit'])->name('time_logs.edit');
    Route::put('/time-logs/{timeLog}', [TimeLogController::class, 'update'])->name('time_logs.update');
    Route::delete('/time-logs/{timeLog}', [TimeLogController::class, 'destroy'])->name('time_logs.destroy');

    // LEAVES CRUD
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

    Route::get('/leaves/{leave}/edit', [LeaveController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
});