<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PEDIDO N° {{ $order->id }}</title>
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
                            <p>Estimado/a <strong>{{ ucwords(strtolower($order->client->client_name)) }}</strong></p>
                            @php(Carbon::setLocale('es'))
                            <p>Le escribimos de parte de <strong>{{ $order->business->name }}</strong> para notificarle que a las <strong>{{ Carbon::parse($order->seller_date)->format('h:i:s A') }}</strong> del día <strong>{{ Carbon::parse($order->seller_date)->isoFormat('D [de] MMMM [de] YYYY') }}</strong>, el vendedor <strong>{{ ucwords(strtolower($order->seller_user->name . ' ' . $order->seller_user->last_name)) }}</strong> cerró un pedido a su nombre, el cual quedó registrado con el numero de pedido <strong>{{ $order->id }}</strong>.</p>
                            <p>Le anexamos un PDF con la información detallada del pedido realizado.</p>
                            <p>Gracias por su atención.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="footer">
                <p>Este es un mensaje generado automáticamente, por favor, no responda a este correo. Si necesita ponerse en contacto con nosotros, consulte los datos en el archivo PDF adjunto.</p>
                <p>En caso de que este correo haya sido recibido por error, está prohibido el uso de los datos contenidos en este email y en el archivo adjunto. Por favor, elimínelo inmediatamente y notifíquenos el error al correo sac@organizacionbless.com.co.</p>
                <p>Derechos reservados © {{ Carbon::now()->format('Y') }} <br> {{ $order->business->name }} <br> {{ $order->business->number_document }} <br> {{ $order->business->address }} <br> {{ $order->business->city }} - {{ $order->business->departament }}</p>
            </div>
        </div>
    </body>
</html>
