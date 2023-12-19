<?php

namespace App\Http\Controllers;

use App\Models\facture;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Rmunate\Utilities\SpellNumber;

class invioce extends Controller
{
    public function downloadPDF($id){


        // $totalToLetter = SpellNumber::value(1500)->locale('fr')->toLetters();
        // return view('invioce');

         $order = Order::where('id',$id)->first();

        // dd($order[0]['totalAmount']);
        $totalToLetter = SpellNumber::value($order->totalAmount)->locale('fr')->toLetters();

     $pdf = Pdf::loadView('invioceOld',compact('totalToLetter','order'));

    return $pdf->stream('invioceOld.pdf');
    }

    public function facturePDF($id){

         $order = facture::where('id',$id)->first();

        $totalToLetter = SpellNumber::value($order->totalTTC)->locale('fr')->toLetters();

     $pdf = Pdf::loadView('facture',compact('totalToLetter','order'));


    return $pdf->stream('invioceOld.pdf');
    }
}
