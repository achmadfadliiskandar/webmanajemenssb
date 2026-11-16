<?php

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Admin\Pemain;
use App\Http\Livewire\Admin\Penilaian;
use App\Http\Livewire\Admin\User;
use App\Http\Livewire\Admin\Absen;
use App\Http\Livewire\Admin\Jadwal;
use App\Http\Livewire\Admin\Dashboard;
use App\Http\Controllers\LaporanController;
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
    return view('welcome');
});

// 1. Publik / Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// 2. Private / Terproteksi
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/pemain', Pemain::class)->name('admin.pemain');
    Route::get('/admin/user', User::class)->name('admin.user');
    Route::get('/admin/jadwal', Jadwal::class)->name('admin.jadwal');
    Route::get('/admin/absen', Absen::class)->name('admin.absen');
    Route::get('/admin/penilaian', Penilaian::class)->name('admin.penilaian');
});

Route::get('/cetak-performa/{schedule_id}', [LaporanController::class, 'cetakPerforma'])->name('cetak.performa');


