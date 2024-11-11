<?php

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login', [MainController::class, 'transform'])->name('Users.login');
Route::post('/signup', [MainController::class, 'transform'])->name('Users.signup');


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [MainController::class, 'transform'])->name('Users.logout');
    Route::get('/profile', [MainController::class, 'transform'])->name('Users.profile');

    Route::post('/create-group', [MainController::class, 'createGroup'])->name('Groups.createGroup');
    Route::post('/invite-collaborators', [MainController::class, 'transform'])->name('Groups.inviteCollaborators');
    Route::post('/response-invitation', [MainController::class, 'transform'])->name('Groups.responseInvitation');

    Route::get('/view-group-members/{group_name}', [MainController::class, 'transform'])->name('Groups.viewGroupMembers');
    Route::get('/view-group-files/{group_name}', [MainController::class, 'transform'])->name('Groups.viewGroupFiles');

    Route::post('/upload-file', [MainController::class, 'transform'])->name('Files.uploadFile');
    Route::post('/download-file', [MainController::class, 'transform'])->name('Files.downloadFile');

    Route::get('/check-out/{group_name}/{file_name}', action: [MainController::class, 'transform'])->name('Files.checkIn');
    Route::post('/check-in/{group_name}/{file_name}', action: [MainController::class, 'transform'])->name('Files.checkOut');



    # plus
    Route::post('/request-join-group', [MainController::class, 'transform'])->name('Users.requestJoinGroup');
    Route::post('/response-request', [MainController::class, 'transform'])->name('Users.responseRequest');

});
