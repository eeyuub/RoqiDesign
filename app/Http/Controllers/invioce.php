<?php

namespace App\Http\Controllers;

use App\Models\facture;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Rmunate\Utilities\SpellNumber;

class invioce extends Controller
{
    public function commandePDF($id){

        $order = Order::where('id',$id)->first();

        $totalToLetter = SpellNumber::value($order->totalAmount)->locale('fr')->toLetters();

        $pdf = Pdf::loadView('commande',compact('totalToLetter','order'));

        return $pdf->stream($order->customer->name .' - '. $order->numeroFacture.'.pdf');
    }

    public function facturePDF($numeroFacture){

        $order = facture::where('numeroFacture',$numeroFacture)->first();

        $totalToLetter = SpellNumber::value($order->totalTTC)->locale('fr')->toLetters();

        $pdf = Pdf::loadView('facture',compact('totalToLetter','order'));

        return $pdf->stream($order->customer->name .' - '. $order->numeroFacture.'.pdf');
    }
}
