<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PresentationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Pdf
Route::get('/get_pdf', [PdfController::class, 'generatePdf']);

// Presentatiion
Route::get('/get_ppx/{id}', [PresentationController::class, 'generatePresentation']);

Route::get('/', function () {
    return view('welcome');
});
