<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Models\Group;



Route::post('/register', [UserController::class, 'register'])->name('Users.createUser');
Route::post('/login', [UserController::class, 'login'])->name('Users.login');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/my-profile', [UserController::class, 'myProfile'])->name('Users.getUserProfile');
    Route::post('/create-group', [GroupController::class, 'createGroup'])->name('Groups.createGroup');
    Route::get('/view-users/{page?}', [UserController::class, 'viewUsers'])->name('Users.viewUsers');
    Route::get('/accept-invitation/{invitation_id}', [GroupController::class, 'acceptInvitation'])->name( 'Groups.acceptInvitation');
    Route::get('/reject-invitation/{invitation_id}', [GroupController::class, 'rejectInvitation'])->name( 'Groups.rejectInvitation');
    
    Route::middleware('GroupOwner')->group(function(){
        Route::post('/{group_name}/invite-users', [GroupController::class, 'inviteUsers'])->name('Groups.inviteUsers');
        Route::get('/{group_name}/kick-from-group/{username}', [GroupController::class, 'kickFromGroup'])->name( 'Groups.kickFromGroup');
    });

    Route::middleware('GroupMember')->group(function (){
        Route::get('/{group_name}/view-users/{page?}', [GroupController::class, 'viewGroupUsers'])->name( 'Groups.viewGroupUsers');
        Route::post('/{group_name}/upload-file', [FileController::class, 'uploadFiles'])->name('Files.uploadFiles');
        Route::get('/{group_name}/exit-group', [GroupController::class, 'exitGroup'])->name( 'Groups.exitGroup');
        Route::post('/{group_name}/check-in', [FileController::class, 'checkIn'])->name('Files.checkIn');
        Route::post('/{group_name}/check-out/{file_id}', [FileController::class, 'checkOut'])->name('Files.checkOut');

    });
    
});
Route::get('/diff',[FileController::class,'diff']);