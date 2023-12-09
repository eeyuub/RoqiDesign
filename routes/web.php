<?php

use App\Http\Controllers\invioce;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
   return redirect('admin');
});

Route::get('/invioce/{id}', [invioce::class,'downloadPDF'])->name('downPDF');
Route::get('/facture/{id}', [invioce::class,'facturePDF'])->name('facturePDF');
