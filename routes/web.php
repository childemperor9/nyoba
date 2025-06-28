<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoaController; // PASTIKAN BARIS INI ADA DAN BENAR

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard'); // Menampilkan view dashboard.blade.php
});

// Rute Resource untuk Master COA
// Ini akan mendaftarkan semua route CRUD (index, create, store, show, edit, update, destroy)
Route::resource('coa', CoaController::class);

// Route khusus untuk halaman utama Master COA (jika diperlukan dan tidak di-handle oleh Route::resource)
// Jika Anda ingin URL '/master-coa' mengarah ke index COA, biarkan baris ini.
// Perhatikan bahwa Route::resource('coa') sudah membuat route 'coa.index' yang defaultnya adalah '/coa'.
Route::get('/master-coa', [CoaController::class, 'index'])->name('coa.index');


// --- Rute Baru untuk Konfigurasi COA, diarahkan ke CoaController ---
// Ini adalah rute untuk halaman daftar konfigurasi COA
Route::get('/konfigurasi-coa', [CoaController::class, 'konfigurasiCoaIndex'])->name('konfigurasi.coa.index');

// Ini adalah rute untuk menampilkan form tambah data konfigurasi COA
Route::get('/konfigurasi-coa/create', [CoaController::class, 'konfigurasiCoaCreate'])->name('konfigurasi.coa.create');

// Ini adalah rute untuk menyimpan data konfigurasi COA yang baru
Route::post('/konfigurasi-coa', [CoaController::class, 'konfigurasiCoaStore'])->name('konfigurasi.coa.store');

// Ini adalah rute untuk menampilkan form edit data konfigurasi COA
// {coaConfiguration} adalah parameter untuk ID konfigurasi COA yang akan diedit
Route::get('/konfigurasi-coa/{coaConfiguration}/edit', [CoaController::class, 'konfigurasiCoaEdit'])->name('konfigurasi.coa.edit');

// Ini adalah rute untuk memperbarui data konfigurasi COA yang sudah ada
Route::put('/konfigurasi-coa/{coaConfiguration}', [CoaController::class, 'konfigurasiCoaUpdate'])->name('konfigurasi.coa.update');

// Ini adalah rute untuk menghapus data konfigurasi COA
Route::delete('/konfigurasi-coa/{coaConfiguration}', [CoaController::class, 'konfigurasiCoaDestroy'])->name('konfigurasi.coa.destroy');
