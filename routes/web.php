<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;




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

// Route::get('/', function () {
//     return view('ocr');
// });

Route::get('/superadmin/create-admin', [AdminController::class, 'create'])->name('admin.create');
Route::post('/superadmin/store-admin', [AdminController::class, 'store'])->name('admin.store');

