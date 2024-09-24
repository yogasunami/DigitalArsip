<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/{id}', [DocumentController::class, 'show'])->name('documents.show');
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
Route::get('/', [ScanController::class, 'home']);
Route::get('/scan',[ScanController::class, 'scanWithWIA']);
Route::get('/scan/index',[ScanController::class, 'index']);
Route::post('/scan/process', [ScanController::class, 'processScan']);
Route::post('/scan/start', [ScanController::class, 'startScan']);
Route::post('/scan/finish', [ScanController::class, 'finishScan']);
Route::post('/upload', [DocumentController::class, 'upload']);
Route::get('/arsip', [DocumentController::class, 'arsip'])->name('arsip');
Route::post('/scan/processOCR', [ScanController::class, 'processOCR']);
Route::post('/scan/generatePDF', [ScanController::class, 'generatePDF']);
Route::get('documents/{id}/download', [DocumentController::class, 'download'])->name('document.download');
Route::get('/home', [ScanController::class, 'home'])->name('home');
Route::post('/scan/runOCROnImages', [DocumentController::class, 'runOCROnImages']);
Route::post('/documents/scanlgsg', [DocumentController::class, 'scanlgsg']);

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

#Route::get('/', function () {
#    return view('welcome');
#});
Route::get('/documents/view/{filename}', function ($filename) {
    $path = storage_path('app/documents/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
});

Route::get('/show-pdf', function () {
    return view('show_pdf');
});

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/upload', function () {
    return view('upload');
})->name('upload');

Route::get('/scan', function () {
    return view('scan'); // Mengarahkan ke resources/views/scan.blade.php
})->name('scan');
