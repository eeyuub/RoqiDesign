<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>{{ $order->customer->name }} - {{ $order->numeroFacture }}</title>

		<style>
		/*
  Common invoice styles. These styles will work in a browser or using the HTML
  to PDF anvil endpoint.
*/

body {
  font-size: 16px;
  font-family: monospace
}

table {
  width: 100%;
  border-collapse: collapse;
}

table tr td {
  padding: 0;
}

table tr td:last-child {
  text-align: right;
}

.bold {
  font-weight: bold;
}

.right {
  text-align: right;
}

.large {
  font-size: 1.2rem;
}

.total {
  font-weight: bold;
  color: #2d2d2d;
}

.logo-container {
  margin: 20px 0 70px 0;
}

.invoice-info-container {
  font-size: 0.875em;
}
.invoice-info-container td {
  padding: 4px 0;
}

.client-name {
  font-size: 1.5em;
  vertical-align: top;
}

.line-items-container {
  margin: 70px 0;
  font-size: 0.875em;
}

.line-items-container th {
  text-align: left;
  color: #999;
  border-bottom: 2px solid #ddd;
  padding: 10px 0 15px 0;
  font-size: 0.75em;
  text-transform: uppercase;
}

.line-items-container th:last-child {
  text-align: right;
}

.line-items-container td {
  padding: 15px 0;
}

.line-items-container tbody tr:first-child td {
  padding-top: 25px;
}

.line-items-container.has-bottom-border tbody tr:last-child td {
  padding-bottom: 25px;
  border-bottom: 2px solid #ddd;
}

.line-items-container.has-bottom-border {
  margin-bottom: 0;
}

.line-items-container th.heading-quantity {
  width: 50px;
}
.line-items-container th.heading-price {
  text-align: right;
  width: 100px;
}
.line-items-container th.heading-subtotal {
  width: 100px;
}

.payment-info {
  width: 38%;
  font-size: 0.75em;
  line-height: 1.5;
}

.footer {
    position: absolute;
    bottom: 0;
  /* margin-top: 100px; */
}

.footer-thanks {
  font-size: 1.125em;
}

.footer-thanks img {
  display: inline-block;
  position: relative;
  top: 1px;
  width: 16px;
  margin-right: 4px;
}

.footer-info {
    text-align: center;
  /* float: right; */
  margin-top: 5px;
  font-size: 0.75em;
  color: #ccc;
}

.footer-info span {
  padding: 0 5px;
  color: black;
}

.footer-info span:last-child {
  padding-right: 0;
}

.page-container {
  display: none;
}
		</style>
	</head>

	<body>
        {{-- <div class="page-container">
            Page
            <span class="page"></span>
            of
            <span class="pages"></span>
          </div> --}}

          <div class="logo-container">
            <img
              style="height: 100px"
              src="https://salty-wave.com/assets/img/logo/roqidesign.png">
          </div>

          <table class="invoice-info-container">



            <tr>


              <td>


                @if($order->customer->name)
                    Client: <strong>{{ $order->customer->name }}</strong>
                @endif

                @if($order->customer->address)
                <br>Adresse: <strong>{{ $order->customer->address }}</strong>
                @endif

                @if($order->customer->phone)
                <br>Telephone: <strong>{{ $order->customer->phone }}</strong>
                @endif

                @if($order->customer->fix)
                <br>Fix: <strong>{{ $order->customer->fix }}</strong>
                @endif

                @if($order->customer->RC)
                <br>RC: <strong>{{ $order->customer->RC }}</strong>
                @endif

                @if($order->customer->ICE)
                <br>ICE: <strong>{{ $order->customer->ICE }}</strong>
                @endif


              </td>
              <td>
                Facture No: <strong>{{ $order->numeroFacture }}</strong><br>
                Facture Date: <strong>{{ $order->factureDate }}</strong>

              </td>


            </tr>

          </table>


          <table class="line-items-container" style="margin-bottom: 0px;">
            <thead>
              <tr>

                <th class="heading-description">Designation</th>
                <th class="heading-quantity">Qty</th>
                <th class="heading-price">Prix</th>
                <th class="heading-subtotal">Soustotal</th>
              </tr>
            </thead>
            <tbody>

                @foreach ($order->factureItems as $item)
                <tr>
                    @if (!empty($item->designation))
                    <td>{{ $item->designation }}</td>
                    @else
                    <td>{{ $item->productOption->option }}</td>
                    @endif

                    <td>{{ $item->quantity }}{{ $item->productOption->productSize->size }}</td>

                    <td class="right">{{ number_format($item->unitPrice, 2, '.', ',') }}DH</td>
                    <td class="bold">{{ number_format($item->totalAmount, 2, '.', ',') }}DH</td>
                  </tr>
                @endforeach

                @foreach ($order->factureExtends as $item)
                <tr>

                    <td>{{ $item->designation }}</td>
                    <td>{{ $item->quantity }}{{ $item->productSize }}</td>
                    <td class="right">{{ number_format($item->unitPrice, 2, '.', ',') }}DH</td>
                    <td class="bold">{{ number_format($item->totalAmount, 2, '.', ',') }}DH</td>
                  </tr>
                @endforeach

            </tbody>
          </table>

          <table class="line-items-container" style="margin-top: 0px; ">
            <thead>
              <tr>
                <th class="heading-quantity"></th>
                <th class="heading-description"></th>
                <th class="heading-price"></th>
                <th class="heading-subtotal"></th>
              </tr>
            </thead>
            <tbody>
              {{-- <tr>
                <td></td>
                <td></td>
                <td class="right">TOTAL HT</td>
                <td class="bold">1200.00DH</td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td class="right"> TVA 20%</td>
                <td class="bold">300.00DH</td>
              </tr> --}}
              <tr>
                <td></td>
                <td></td>
                   {{-- <td></td> --}}
                   <td class="right"> TOTAL HT</td>
                   <td class="bold">{{ number_format($order->totalHT, 2, '.', ',')}}DH</td>
                </tr>
              <tr>
                <td></td>
                <td></td>
                   {{-- <td></td> --}}
                   <td class="right"> TAX(20%)</td>
                   <td class="bold">{{ number_format(($order->totalHT*$order->tva)/100, 2, '.', ',')}}DH</td>
                </tr>
              <tr>
             <td></td>
             <td></td>
                {{-- <td></td> --}}
                <td class="right"> TOTAL TTC</td>
                <td class="bold">{{ number_format($order->totalTTC, 2, '.', ',') }}DH</td>
              </tr>
            </tbody>
          </table>

          <div>
            <p style="font-size: 13px;font-weight:500;letter-spacing :1px">Arretee la Presente facture  a la somme  de :</p>
            <p style="font-size: 13px;"> {{ $totalToLetter }} dirhams</p>

            {{-- <td rowspan="2"></td> --}}

          </div>


          <div class="footer">
            <div class="footer-info">
                <span>
                Siam N° Raz De chaussee Bd.Haj Habib Marins Pêcheurs-AGADIR-R.C:4511  ICE: 002683065000046 Fix:+212528382788/tel:+212661104297/Email:roqidesign@gmail.com
            </span>{{-- <span>hello@useanvil.com</span> |
              <span>555 444 6666</span> |
              <span>useanvil.com</span> --}}
            </div>

          </div>
	</body>
</html>
