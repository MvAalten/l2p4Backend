<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Friends
    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::post('/friends/request', [FriendController::class, 'sendRequest'])->name('friends.request');
    Route::patch('/friends/{friendship}/accept', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::patch('/friends/{friendship}/decline', [FriendController::class, 'declineRequest'])->name('friends.decline');

    // Games
    Route::resource('games', GameController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/games/{game}/accept', [GameController::class, 'acceptInvitation'])->name('games.accept');
    Route::post('/games/{game}/guess', [GameController::class, 'makeGuess'])->name('games.guess');

    // Comments
    Route::post('/games/{game}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    // Profile
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    // Friendships
    Route::patch('/friends/{friendship}/accept', [FriendController::class, 'acceptRequest'])
        ->name('friends.accept')
        ->whereNumber('friendship');

    Route::patch('/friends/{friendship}/decline', [FriendController::class, 'declineRequest'])
        ->name('friends.decline')
        ->whereNumber('friendship');
});
