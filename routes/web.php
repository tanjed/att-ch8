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

    Route::prefix('user')->name('user.')->middleware('verified')->group(function () {
        Route::post('credentials/test', [\App\Http\Controllers\User\CredentialController::class, 'test'])->name('credentials.test');
        Route::resource('credentials', \App\Http\Controllers\User\CredentialController::class);
        Route::resource('actions', \App\Http\Controllers\User\ActionSettingController::class);
    });
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function (\Illuminate\Http\Request $request) {
        $users = \App\Models\User::whereHas('actionSettings')->orWhereHas('credentials')->orderBy('name')->get();

        $query = \App\Models\UserActionSetting::with(['user', 'platformAction.platform']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('platform_id')) {
            $query->whereHas('platformAction', function ($q) use ($request) {
                $q->where('platform_id', $request->platform_id);
            });
        }

        if ($request->filled('action_id')) {
            $query->where('platform_action_id', $request->action_id);
        }

        $allActions = $query->orderBy('next_execution_time', 'asc')->get();
        $selectedUserId = $request->user_id;
        $selectedPlatformId = $request->platform_id;
        $selectedActionId = $request->action_id;

        $platforms = \App\Models\Platform::orderBy('name')->get();
        $actions = \App\Models\PlatformAction::with('platform')->orderBy('name')->get();

        // KPI Calculations
        $totalCredentials = \App\Models\UserPlatformCredential::count();
        $totalActions = \App\Models\UserActionSetting::count();
        $totalUsers = \App\Models\User::count();
        $verifiedUsers = \App\Models\User::whereNotNull('email_verified_at')->count();
        $todayExecutions = \App\Models\ActionLog::whereDate('created_at', today())->count();
        $todaySuccess = \App\Models\ActionLog::whereDate('created_at', today())->where('status', 'success')->count();
        $todayFailed = \App\Models\ActionLog::whereDate('created_at', today())->where('status', 'failed')->count();

        return view('admin.dashboard', compact(
            'allActions',
            'users',
            'platforms',
            'actions',
            'selectedUserId',
            'selectedPlatformId',
            'selectedActionId',
            'totalCredentials',
            'totalActions',
            'totalUsers',
            'verifiedUsers',
            'todayExecutions',
            'todaySuccess',
            'todayFailed'
        ));
    })->name('dashboard');

    Route::resource('platforms', \App\Http\Controllers\Admin\PlatformController::class);
    Route::resource('actions', \App\Http\Controllers\Admin\PlatformActionController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'edit', 'update']);
});

require __DIR__ . '/auth.php';
