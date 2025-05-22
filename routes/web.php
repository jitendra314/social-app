<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookLoginController;

Route::get('/', function () {
    return redirect('/dashboard');
});
Route::get('/login/facebook', [FacebookLoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [FacebookLoginController::class, 'handleFacebookCallback']);
// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User search
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Friend request routes
    Route::get('/friends', [FriendController::class, 'friendList'])->name('friends.list');
    Route::get('/friend-request/send/{id}', [FriendController::class, 'sendRequest'])->name('friend.request.send');
    Route::get('/friend-requests', [FriendController::class, 'requests'])->name('friend.requests');
    Route::get('/friend-request/accept/{id}', [FriendController::class, 'acceptRequest'])->name('friend.request.accept');

    // Profile with mutual friends
    Route::get('/profile/{id}', [FriendController::class, 'profile'])->name('user.profile');
    Route::get('/friend-request/accept-from-search/{senderId}', [FriendController::class, 'acceptFromSearch'])->name('friend.request.accept.from.search');

    Route::view('/privacy-policy', 'privacy-policy');
    Route::view('/data-deletion', 'data-deletion');

});

require __DIR__.'/auth.php';
