<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ $order->customer->name }} - {{ $order->orderNumber }}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
*,
*::after,
*::before{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

:root{
    --blue-color: #0c2f54;
    --dark-color: #ffffff;
    --white-color: #fff;
}

ul{
    list-style-type: none;
}
ul li{
    margin: 2px 0;
}

/* text colors */
.text-dark{
    color: var(--dark-color);
}
.text-blue{
    color: var(--blue-color);
}
.text-end{
    text-align: right;
}
.text-center{
    text-align: center;
}
.text-start{
    text-align: left;
}
.text-bold{
    font-weight: 700;
}
/* hr line */
/* .hr{
    height: 1px;
    background-color: rgba(0, 0, 0, 0.1);
}
/* border-bottom */
.border-bottom{
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
} */

body{
    font-family: 'Poppins', sans-serif;
    color: var(--dark-color);
    font-size: 14px;
}
.invoice-wrapper{
    min-height: 100vh;
    background-color: rgba(0, 0, 0, 0.1);
    padding-top: 20px;
    padding-bottom: 20px;
}
.invoice{
    max-width: 850px;
    margin-right: auto;
    margin-left: auto;
    background-color: var(--white-color);
    padding: 70px;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    min-height: 920px;
}
.invoice-head-top-left img{
    width: 130px;
}
.invoice-head-top-right h3{
    font-weight: 500;
    font-size: 27px;
    color: var(--blue-color);
}
.invoice-head-middle, .invoice-head-bottom{
    padding: 16px 0;
}
.invoice-body{
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    overflow: hidden;
}
.invoice-body table{
    border-collapse: collapse;
    border-radius: 4px;
    width: 100%;
}
.invoice-body table td, .invoice-body table th{
    padding: 12px;
}
.invoice-body table tr{
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}
.invoice-body table thead{
    background-color: rgba(0, 0, 0, 0.02);
}
.invoice-body-info-item{
    display: grid;
    grid-template-columns: 80% 20%;
}
.invoice-body-info-item .info-item-td{
    padding: 12px;
    background-color: rgba(0, 0, 0, 0.02);
}
.invoice-foot{
    padding: 30px 0;
}
.invoice-foot p{
    font-size: 12px;
}
.invoice-btns{
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
.invoice-btn{
    padding: 3px 9px;
    color: var(--dark-color);
    font-family: inherit;
    border: 1px solid rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.invoice-head-top, .invoice-head-middle, .invoice-head-bottom{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    padding-bottom: 10px;
}

@media screen and (max-width: 992px){
    .invoice{
        padding: 40px;
    }
}

@media screen and (max-width: 576px){
    .invoice-head-top, .invoice-head-middle, .invoice-head-bottom{
        grid-template-columns: repeat(1, 1fr);
    }
    .invoice-head-bottom-right{
        margin-top: 12px;
        margin-bottom: 12px;
    }
    .invoice *{
        text-align: left;
    }
    .invoice{
        padding: 28px;
    }
}

.overflow-view{
    overflow-x: scroll;
}
.invoice-body{
    min-width: 600px;
}

@media print{
    .print-area{
        visibility: visible;
        width: 100%;
        position: absolute;
        left: 0;
        top: 0;
        overflow: hidden;
    }

    .overflow-view{
        overflow-x: hidden;
    }

    .invoice-btns{
        display: none;
    }
}

.footer {
    position: absolute;
    bottom: 0;
}


        </style>
    </head>
    <body>

        <div class = "" id = "print-area">
            <div class = "invoice">
                <div class = "invoice-container">
                    <div class = "invoice-head">
                        <div class = "invoice-head-top">
                            <div class = "invoice-head-top-left text-start">
                                <img src="https://salty-wave.com/assets/img/logo/roqidesign.png">
                            </div>
                            <div class = "invoice-head-top-right text-end">
                                <h3>Facture</h3>
                            </div>
                        </div>
                        <div class = "hr"></div>
                        <div class = "invoice-head-middle">
                            <div class = "invoice-head-middle-left text-start">
                                <p><span class = "text-bold">Date</span>: {{ $order->orderDate }}</p>
                            </div>
                            <div class = "invoice-head-middle-right text-end">
                                <p><span class = "text-bold">Facture No:</span>{{ $order->orderNumber }}</p>
                            </div>
                        </div>
                        <div class = "hr"></div>
                        <div class = "invoice-head-bottom">
                            <div class = "invoice-head-bottom-left">
                                <ul>
                                    <li class = 'text-bold'>Facturé à:</li>
                                    <li>{{ $order->customer->name }}</li>
                                    <li>{{ $order->customer->address }}</li>
                                    <li>{{ $order->customer->phone }}</li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class = "overflow-view">
                        <div class = "invoice-body">
                            <table>
                                <thead>
                                    <tr>
                                        <td class = "text-bold">Service</td>
                                        <td class = "text-bold">Description</td>
                                        <td class = "text-bold">PU</td>
                                        <td class = "text-bold">QTY</td>
                                        <td class = "text-bold">Amount</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderProducts as $item)
                                    <tr>
                                        {{-- <td class = "text-bold">Amount</td> --}}
                                        <td>{{ $item->productOption->option }}</td>
                                        <td class="right">{{ $item->unitPrice }}DH</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="bold">{{ $item->totalAmount }}DH</td>
                                      </tr>
                                    @endforeach
                                    <tr>
                                        {{-- <td class = "text-bold">Amount</td> --}}
                                        <td></td>
                                        <td class="right"></td>
                                        <td></td>
                                        <td class="bold">Total: {{ number_format($order->totalAmount, 2, '.', ',') }}DH</td>
                                      </tr>
                                    {{-- <tr>
                                        <td>Design</td>
                                        <td>Creating a website design</td>
                                        <td>$50.00</td>
                                        <td>10</td>
                                        <td class = "text-end">$500.00</td>
                                    </tr>
                                    <tr>
                                        <td>Development</td>
                                        <td>Website Development</td>
                                        <td>$50.00</td>
                                        <td>10</td>
                                        <td class = "text-end">$500.00</td>
                                    </tr>
                                    <tr>
                                        <td>SEO</td>
                                        <td>Optimize the site for search engines (SEO)</td>
                                        <td>$50.00</td>
                                        <td>10</td>
                                        <td class = "text-end">$500.00</td>
                                    </tr> --}}
                                    <!-- <tr>
                                        <td colspan="4">10</td>
                                        <td>$500.00</td>
                                    </tr> -->

                                </tbody>
                            </table>

                        </div>
                    </div>


                <div class = "invoice-head-bottom-left">
                    <ul>
                        <li class = 'text-bold'>Arretee la Presente facture  a la somme  de :</li>
                        <li>{{ $totalToLetter }} dirhams</li>
                        {{-- <li>15 Hodges Mews, High Wycombe</li>
                        <li>HP12 3JL</li>
                        <li>United Kingdom</li> --}}
                    </ul>
                </div>


            </div>
        </div>
        <div class="footer">
            <div class="footer-info">
                <span>
                Wiam N° Raz De chaussee Bd.Haj Habib Marins Pêcheurs-AGADIR-R.C:4511  ICE: 002683065000046 Fix:+212528382788/tel:+212661104297/Email:roqidesign@gmail.com
            </span>
            </div>
        </div>
        <script src = "script.js"></script>
    </body>
</html>
