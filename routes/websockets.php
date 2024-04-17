<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:api', 'jwt.verify']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Route::get('/user-list', [UserController::class, 'UserList'])
    ->name('user-list')
    ->middleware(['auth:api', 'jwt.verify']);
