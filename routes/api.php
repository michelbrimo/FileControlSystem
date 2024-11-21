<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;

Route::post('/register', [UserController::class, 'register'])->name('Users.createUser');
Route::post('/login', [UserController::class, 'login'])->name('Users.login');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/my-profile', [UserController::class, 'myProfile'])->name('Users.getUserProfile');

    Route::post('/create-group', [GroupController::class, 'createGroup'])->name('Groups.createGroup');    
    Route::get('/view-users/{page?}', [UserController::class, 'viewUsers'])->name('Users.viewUsers');
    Route::post('/invite-users', [GroupController::class, 'inviteUsers'])->name('Groups.inviteUsers');
    
    Route::get('/accept-invitation/{invitation_id}', [GroupController::class, 'acceptInvitation'])->name( 'Groups.acceptInvitation');
    Route::get('/reject-invitation/{invitation_id}', [GroupController::class, 'rejectInvitation'])->name( 'Groups.rejectInvitation');

    Route::get('/{group_name}/view-users/{page?}', [GroupController::class, 'viewGroupUsers'])->name( 'Groups.viewGroupUsers');

    Route::post('/{group_name}/upload-file/{file_id?}', [FileController::class, 'uploadFiles'])->name('Files.uploadFiles');

});
Route::get('/diff',[FileController::class,'diff']);