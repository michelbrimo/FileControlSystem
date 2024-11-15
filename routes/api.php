<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;

Route::post('/register', [UserController::class, 'register'])->name('Users.createUser');
Route::post('/login', [UserController::class, 'login'])->name('Users.login');


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/my-profile', [UserController::class, 'myProfile'])->name('Users.getUserProfile');
    Route::post('/create-group', [GroupController::class, 'createGroup'])->name('Groups.createGroup');
});