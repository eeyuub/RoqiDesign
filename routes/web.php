<?php

use App\Http\Controllers\invioce;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
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
   return redirect('admin');
});

Route::get('/invioce/{id}', [invioce::class,'downloadPDF'])->name('downPDF');
Route::get('/facture/{id}', [invioce::class,'facturePDF'])->name('facturePDF');

