<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\LineWebhookController;
use App\Http\Controllers\LineNotifyProjectController;

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

// //Google
// Route::get('/login/google', [App\Http\Controllers\Auth\SocialiteController::class, 'redirectToGoogle'])->name('login.google');
// Route::get('/login/google/callback', [App\Http\Controllers\Auth\SocialiteController::class, 'handleGoogleCallback']);
//Facebook
Route::get('/login/facebook', [App\Http\Controllers\Auth\SocialiteController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [App\Http\Controllers\Auth\SocialiteController::class, 'handleFacebookCallback']);
// //Github
// Route::get('/login/github', [App\Http\Controllers\Auth\SocialiteController::class, 'redirectToGithub'])->name('login.github');
// Route::get('/login/github/callback', [App\Http\Controllers\Auth\SocialiteController::class, 'handleGithubCallback']);

Route::get('/', function () {
    return view('home');
});

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Line Web hook
Route::post('/line/webhook', [LineWebhookController::class, 'webhook']);


Route::get('/line-notify', [LineNotifyProjectController::class, 'index']);
Route::get('/line-notify/callback', [LineNotifyProjectController::class, 'callback']);