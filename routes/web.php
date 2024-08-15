<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('index');
})->name('index');

Route::post('/upload', [FileUploadController::class, 'upload']);
Route::get('/decrypt/{fileName}', [FileUploadController::class, 'decryptFileName']);
Route::get('/list', [FileUploadController::class, 'listUploads'])->name('uploads.list');
Route::get('/file/decrypt/{id}', [FileUploadController::class, 'decryptFile'])->name('file.decrypt');