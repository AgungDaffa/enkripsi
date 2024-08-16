<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::post('/upload', [FileUploadController::class, 'upload']);

Route::get('/decrypt/{fileName}', [FileUploadController::class, 'decryptFileName']);

// Rute untuk halaman daftar enkripsi
Route::get('/uploads/encryption', [FileUploadController::class, 'showEncryptionList'])->name('uploads.encryption');

// Rute untuk halaman daftar dekripsi
Route::get('/uploads/decryption', [FileUploadController::class, 'showDecryptionList'])->name('uploads.decryption');

// Rute untuk mendownload file enkripsi
Route::get('/file/download-encryption/{id}', [FileUploadController::class, 'downloadEncryptedFile'])->name('file.download.encryption');

// Rute untuk mendekripsi file berdasarkan ID
Route::get('/file/decrypt/{id}', [FileUploadController::class, 'decryptFile'])->name('file.decrypt');
