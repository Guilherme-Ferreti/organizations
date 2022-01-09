<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Domains\Organization\Controllers\InterestController;
use App\Domains\Organization\Controllers\OrganizationController;
use App\Domains\Organization\Controllers\OrganizationMemberController;
use App\Domains\Organization\Controllers\OrganizationTypeController;

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
        Route::get('/types', [OrganizationTypeController::class, 'index'])->name('types.index');

        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::get('/{organization:uuid}', [OrganizationController::class, 'show'])->name('show');
        Route::put('/{organization:uuid}', [OrganizationController::class, 'update'])->name('update');
        Route::delete('/{organization:uuid}', [OrganizationController::class, 'destroy'])->name('destroy');

        Route::post('/{organization:uuid}/members', [OrganizationMemberController::class, 'store'])->name('members.store');
        Route::put('/{organization:uuid}/members/{member}', [OrganizationMemberController::class, 'update'])->name('members.update');
        Route::delete('/{organization:uuid}/members/{member}', [OrganizationMemberController::class, 'destroy'])->name('members.destroy');
        Route::patch('/{organization:uuid}/members/transfer-ownership', [OrganizationMemberController::class, 'transferOwnership'])->name('members.transfer_ownership');
    });
