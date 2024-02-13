<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', LoginController::class);

Route::get('travels',[TravelController::class, 'index']);
Route::get('travels/{Travel:slug}/tours',[TourController::class, 'index']);
Route::middleware('auth:sanctum')->group(function() {

});

Route::prefix('admin')->middleware(['auth:sanctum','role:admin'])->group(function () {
    Route::post('travels',[Admin\TravelController::class,'store']);
});
