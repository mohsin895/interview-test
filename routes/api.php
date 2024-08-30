<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/category/get',[App\Http\Controllers\ProductController::class,'getCagetgory']);
Route::get('/product/get',[App\Http\Controllers\ProductController::class,'getProduct']);
Route::post('/product/insert',[App\Http\Controllers\ProductController::class,'productInsert']);
Route::post('/product/delete', [App\Http\Controllers\ProductController::class, 'dataInfoDelete']);
Route::get('/product/info',[App\Http\Controllers\ProductController::class,'productInfo']);
Route::post('/product/update',[App\Http\Controllers\ProductController::class,'productUpdate']);

Route::group(['prefix'=>'category'], function(){
    Route::get('/get/list', [App\Http\Controllers\CategoryController::class,'index']);
    Route::post('/insert/update', [App\Http\Controllers\CategoryController::class, 'dataInfoAddOrUpdate']);
    Route::post('/delete', [App\Http\Controllers\CategoryController::class, 'dataInfoDelete']);

});