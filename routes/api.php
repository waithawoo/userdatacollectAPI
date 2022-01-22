<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NormaluserController;
use App\Http\Controllers\Api\OccupationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::fallback(function () {
//     return response()->json(['error' => 'Data Not Found!'], 404);
// });

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function () {// api authentication middleware
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth-user', [AuthController::class, 'user']);    

    Route::post('/occupations/add', [OccupationController::class, 'add']);
    Route::put('/occupations/update/{occupation_uuid}', [OccupationController::class, 'update']);

    Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {// admin authorization middleware
        Route::get('/all_members', [AdminController::class, 'getAllMembers']);
        Route::get('/member/{user_uuid}', [AdminController::class, 'getMember']);
    });
    
    Route::group(['middleware' => 'normal-user', 'prefix' => 'member'], function () {// normal-user authorization middleware
        Route::get('/me', [NormaluserController::class, 'getMember']);
        Route::delete('/delete_membership_code', [NormaluserController::class, 'deleteMembershipCode']);
    });

});