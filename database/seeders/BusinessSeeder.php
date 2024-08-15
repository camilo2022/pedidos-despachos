<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Business::create([
            'name' => 'ORGANIZACIÓN BLESS S.A.S',
            'branch' => 'BLESS CUCUTA',
            'number_document' => '900835084-7',
            'country' => 'COLOMBIA',
            'departament' => 'NORTE DE SANTANDER',
            'city' => 'CUCUTA',
            'address' => 'Cll 17 N # 5-65 ZONA INDUSTRIAL',
            'order_footer' => 'Servicio al Cliente: Para cualquier consulta, solicitud de asistencia, inquietud sobre facturacion, por favor
                contactenos a traves de sac@organizacionbless.com.co o al +57 315 2820926.
                Cartera y Pagos: Para asuntos relacionados con pagos o verificacion, por favor envie un correo electronico a
                gestiondecobranza@organizacionbless.com.co o llame al +57 321 7770013.
                Vendedor Asignado: Para consultas sobre productos o para realizar pedidos adicionales, comuniquese
                directamente con su vendedor asignado.',
            'dispatch_footer' => '',
            'packing_footer' => 'Está caja es propiedad de la ORGANIZACIÓN BLESS S.A.S, en caso de pérdida favor comunicarse a los siguientes números de contacto
                Tel:  (7) 5956487           Cel:  3107506812 - 3112520687'
        ]);

        Business::create([
            'name' => 'ORGANIZACIÓN BLESS S.A.S',
            'branch' => 'BLESS MEDELLIN',
            'number_document' => '900835084-7',
            'country' => 'COLOMBIA',
            'departament' => 'ANTIOQUIA',
            'city' => 'MEDELLIN',
            'address' => 'CR 53 # 47-01 CC MEGACENTRO LC 921 TORRE PICHINCHA',
            'order_footer' => 'Servicio al Cliente: Para cualquier consulta, solicitud de asistencia, inquietud sobre facturacion, por favor
                contactenos a traves de facturacionmedellin@organizacionbless.com.co o al +57 314 4167512.
                Cartera y Pagos: Para asuntos relacionados con pagos o verificacion, por favor envie un correo electronico a
                carteramed@organizacionbless.com.co o llame al +57 313 5563653.
                Vendedor Asignado: Para consultas sobre productos o para realizar pedidos adicionales, comuniquese
                directamente con su vendedor asignado.',
            'dispatch_footer' => '',
            'packing_footer' => 'Está caja es propiedad de la ORGANIZACIÓN BLESS S.A.S, en caso de pérdida favor comunicarse a los siguientes números de contacto
                Tel:  (7) 5956487           Cel:  3144167512 - 3135563653'
        ]);
    }
}