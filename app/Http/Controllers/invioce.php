<?php

namespace App\Http\Controllers;

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


    //  return view('invioce',compact('order','totalToLetter'));
     /* $contxt = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ]); */
    //   $pdf->setPaper('A4', 'portrait');
    //   $pdf->getDomPDF()->setHttpContext($contxt);
    return $pdf->stream('invioceOld.pdf');
    }
}
