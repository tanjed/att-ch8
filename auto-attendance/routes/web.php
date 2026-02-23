<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $credentialsCount = $user->credentials()->count();
    $scheduledActionsCount = $user->actionSettings()->count();
    $recentLogs = \App\Models\ActionLog::with('platformAction.platform')
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('dashboard', compact('credentialsCount', 'scheduledActionsCount', 'recentLogs'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('user')->name('user.')->group(function () {
        Route::resource('credentials', \App\Http\Controllers\User\CredentialController::class);
        Route::resource('actions', \App\Http\Controllers\User\ActionSettingController::class);
    });
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('platforms', \App\Http\Controllers\Admin\PlatformController::class);
    Route::resource('actions', \App\Http\Controllers\Admin\PlatformActionController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'edit', 'update']);
});

require __DIR__ . '/auth.php';
