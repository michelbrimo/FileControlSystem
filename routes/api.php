<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'register'])->name('Users.register');

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/create-group', [GroupController::class, 'createGroup'])->name('Groups.createGroup');

});