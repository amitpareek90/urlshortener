<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [ShortUrlController::class, 'index'])->name('dashboard');
    Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    });
});

Route::get('/{code}', RedirectController::class)
    ->where('code', '[A-Za-z0-9]{6}')
    ->name('short-urls.redirect');
