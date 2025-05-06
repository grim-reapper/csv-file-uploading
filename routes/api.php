<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/uploads', [FileUploadController::class, 'store']);
Route::get('/uploads', [FileUploadController::class, 'index']);
Route::get('/uploads/{fileUpload}', [FileUploadController::class, 'show']);