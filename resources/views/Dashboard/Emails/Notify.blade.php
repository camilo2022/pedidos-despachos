<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PEDIDO N° {{ $order->id }}</title>
        <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome-free/css/all.min.css') }}">
    
        <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
        <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-solid.css">
        <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-regular.css">
        <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-light.css">
        <style>
            body {
                font-family: 'Trebuchet MS', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border: 1px solid #dddddd;
            }
            table {
                padding: 20px 40px 0 40px;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                color: #777777;
                padding: 0 40px 0 40px;
            }
            img {
                padding: 0px;
            }
            .no-padding {
                padding: 0;
            }
            .image-container img {
                display: block;
                margin: 0px auto;
                padding: 0;
                text-align: center; 
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <table>
                <thead class="no-padding">
                    <tr class="no-padding">
                        <th width="20%">
                            <div class="image-container">
                                <img src="https://orgbless.com/wp-content/uploads/2022/01/logo-bless.jpg" alt="ORGANIZACIÓN BLESS S.A.S">
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p>Estimado equipo de Cartera,</p>
                            <p>
                                @php(Carbon::setLocale('es'))
                                Les informamos que el vendedor <strong>{{ ucwords(strtolower($order->seller_user->name . ' ' . $order->seller_user->last_name)) }}</strong> ha asentado un nuevo pedido para el cliente <strong> {{ ucwords(strtolower($order->client->client_name)) }} </strong> 
                                identificado con <strong> {{ $order->client->client_number_document }}-{{ $order->client->client_branch_code }} </strong> registrado con el numero de pedido <strong>{{ $order->id }}</strong>.
                                El pedido fue registrado a las a las <strong>{{ Carbon::parse($order->created_at)->format('h:i:s A') }}</strong> del día <strong>{{ Carbon::parse($order->created_at)->isoFormat('D [de] MMMM [de] YYYY') }}</strong>  y cerrado a las a las <strong>{{ Carbon::parse($order->seller_date)->format('h:i:s A') }}</strong> del día <strong>{{ Carbon::parse($order->seller_date)->isoFormat('D [de] MMMM [de] YYYY') }}</strong>.
                            </p>
                            <p>Para gestionar el pedido, puede hacer click <a href="{{ URL::route('Dashboard.Orders.Details.Index', ['id' => $order->id]) }}">AQUÍ</a>.
                            <p>¡Que tenga un excelente día! <i class="far fa-face-smile"></i></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="footer">
                <p>Derechos reservados © {{ Carbon::now()->format('Y') }} <br> {{ $order->business->name }} <br> {{ $order->business->number_document }} <br> {{ $order->business->address }} <br> {{ $order->business->city }} - {{ $order->business->departament }}</p>
            </div>
        </div>
    </body>
</html>
