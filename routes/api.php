<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;


Route::post('/register', [UserController::class, 'register'])->name('Users.createUser');
Route::post('/login', [UserController::class, 'login'])->name('Users.login');


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/my-profile', [UserController::class, 'myProfile'])->name('Users.getUserProfile');
    Route::post('/create-group', [GroupController::class, 'createGroup'])->name('Groups.createGroup');
    Route::get('/view-users/{page?}', [UserController::class, 'viewUsers'])->name('Users.viewUsers');
    Route::get('/accept-invitation/{invitation_id}', [GroupController::class, 'acceptInvitation'])->name( 'Groups.acceptInvitation');
    Route::get('/reject-invitation/{invitation_id}', [GroupController::class, 'rejectInvitation'])->name( 'Groups.rejectInvitation');
    Route::get('/view-my-invitations/{page?}', [GroupController::class, 'viewMyInvitations'])->name('Groups.viewMyInvitations');
    Route::get('/view-groups/{page?}', [GroupController::class, 'viewGroups'])->name('Groups.viewGroups');
    Route::get('/view-my-groups/{page?}', [GroupController::class, 'viewMyGroups'])->name('Groups.viewMyGroups');


    Route::middleware('GroupOwner')->group(function(){
        Route::post('/{group_name}/invite-users', [GroupController::class, 'inviteUsers'])->name('Groups.inviteUsers');
        Route::get('/{group_name}/kick-from-group/{username}', [GroupController::class, 'kickFromGroup'])->name( 'Groups.kickFromGroup');
        Route::get('/{group_name}/delete-file/{file_id}', [FileController::class, 'deleteFile'])->name( 'Files.deleteFile');
    });

    Route::middleware('GroupMember')->group(function (){
        Route::get('/{group_name}/view-users/{page?}', [GroupController::class, 'viewGroupUsers'])->name('Groups.viewGroupUsers');
        Route::post('/{group_name}/upload-file', [FileController::class, 'uploadFiles'])->name('Files.uploadFiles');
        Route::get('/{group_name}/exit-group', [GroupController::class, 'exitGroup'])->name( 'Groups.exitGroup');
        Route::post('/{group_name}/check-in', [FileController::class, 'checkIn'])->name('Files.checkIn');
        Route::post('/{group_name}/download', [FileController::class, 'download'])->name('Files.download');
        Route::post('/{group_name}/check-out/{file_id}', [FileController::class, 'checkOut'])->name('Files.checkOut');
        Route::get('/{group_name}/view-files/{page?}', [FileController::class, 'viewGroupFiles'])->name('Files.viewGroupFiles');
        Route::get('/{group_name}/view-file-details/{file_id}/{page?}', [FileController::class, 'viewGroupFileDetails'])->name('Files.viewGroupFileDetails');
        Route::get('/{group_name}/view-file-detail-content/{file_detail_id}', [FileController::class, 'viewFileDetailContent'])->name('Files.viewFileDetailContent');
        Route::get('/{group_name}/see-changes/{file_id}', [FileController::class, 'seeChanges'])->name('Files.seeChanges');
        Route::get('/{group_name}/see-user-changes/{file_id}/{user_id}', [FileController::class, 'seeUserChanges'])->name('Files.seeUserChanges');
        Route::post('/{group_name}/compare-files',[FileController::class,'compareFiles'])->name('Files.compareFiles');
    });

    Route::middleware('Admin')->group(function(){
        Route::get('/tracing/{user_id}/{page?}', [UserController::class, 'tracing'])->name('Users.tracing');
    });
});

# test api to download file

