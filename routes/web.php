<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\DokumenUserController;
use App\Http\Controllers\DraftDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CreateUser;
use Illuminate\Support\Facades\Auth;

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
    return view('welcome');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Auth::routes();


Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
// Rute untuk menghapus dokumen dari draft
Route::delete('draft-dokumen/{id}', [DraftDocumentController::class, 'delete'])->name('draft.delete');

// Rute untuk memindahkan dokumen dari draft ke list dokumen
Route::post('draft-dokumen/unarchive/{id}', [DraftDocumentController::class, 'unarchive'])->name('draft-dokumen.unarchive');

// routes/web.php
Route::get('dokumen/{id}/history', [DokumenController::class, 'history'])->name('dokumen.history');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/input-dokumen', [DokumenController::class, 'input'])->name('input-dokumen');
Route::get('/list-dokumen', [DokumenController::class, 'listDokumen'])->name('list-dokumen');
Route::get('/list-dokumen-user', [DokumenUserController::class, 'listDokumen'])->name('list-dokumen-user');
Route::post('simpan-dokumen', [DokumenController::class, 'store'])->name('simpan-dokumen');
Route::post('list-dokumen/process', [DokumenController::class, 'processList'])->name('list-dokumen.process');
Route::get('/kategori-dokumen', [DokumenController::class, 'kategori'])->name('kategori-dokumen');
Route::get('/dokumen/{id}/edit', [DokumenController::class, 'edit'])->name('dokumen.edit');
Route::put('/dokumen/{id}', [DokumenController::class, 'update'])->name('dokumen.update');
Route::get('/search-dokumen', [DokumenController::class, 'search'])->name('search-dokumen');
Route::post('/dokumen/{id}/move-to-draft', [DokumenController::class, 'moveToDraft'])->name('dokumen.moveToDraft');
Route::get('/draft', [DraftDocumentController::class, 'index'])->name('draft-dokumen');
Route::delete('/draft/{id}', [DraftDocumentController::class, 'delete'])->name('draft.delete');
Route::get('/dokumens', [DraftDocumentController::class, 'index'])->name('dokumens.index');
Route::delete('/dokumens/{id}/draft', [DraftDocumentController::class, 'moveToDraft'])->name('dokumens.moveToDraft');
Route::get('/draft-dokumen', [DraftDocumentController::class, 'index'])->name('draft-dokumen');
Route::get('/about-me', [UserController::class, 'aboutMe'])->name('about-me');
Route::get('/input-user', [CreateUser::class, 'create'])->name('input-user');
Route::post('/simpan-user', [CreateUser::class, 'store'])->name('simpan-user');
Route::get('/list-user', [CreateUser::class, 'listUser'])->name('list-user');
Route::get('/get-user-name', [DokumenController::class, 'getUserName']);
Route::get('/list-user/{id}/edit', [CreateUser::class, 'edit'])->name('edit-user');
Route::put('/list-user/{id}', [CreateUser::class, 'update'])->name('update-user');
Route::put('/list-user/{id}/approve', [CreateUser::class, 'approveUser'])->name('approve-user');
Route::get('/list-user', [CreateUser::class, 'index'])->name('list-user');
Route::get('/draft', [DraftDocumentController::class, 'index'])->name('draft-dokumen');
Route::delete('/draft/{id}', [DraftDocumentController::class, 'delete'])->name('draft.delete');
Route::get('/dokumens', [DraftDocumentController::class, 'index'])->name('dokumens.index');
Route::delete('/dokumens/{id}/draft', [DraftDocumentController::class, 'moveToDraft'])->name('dokumens.moveToDraft');
Route::get('/draft-dokumen', [DraftDocumentController::class, 'index'])->name('draft-dokumen');
Route::put('/approve-all', [CreateUser::class, 'approveAll'])->name('approve-all');
Route::put('/cancel-all', [CreateUser::class, 'cancelAll'])->name('cancel-all');
Route::get('/get-nama-dokumen', [DokumenController::class, 'getNamaDokumen']);




