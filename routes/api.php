<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Domains\Organization\Controllers\InterestController;
use App\Domains\Organization\Controllers\OrganizationController;
use App\Domains\Organization\Controllers\OrganizationMemberController;

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

Route::prefix('/auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    });

Route::prefix('/organizations')
    ->name('organizations.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/interests', [InterestController::class, 'index'])->name('interests.index');

        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::put('/{organization:uuid}', [OrganizationController::class, 'update'])->name('update');

        Route::post('/{organization:uuid}/members', [OrganizationMemberController::class, 'store'])->name('members.store');
    });
