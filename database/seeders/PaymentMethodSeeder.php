<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'name' => 'Pagos en efectivo.',
            'is_cash' => true,
            'is_libranza' => false,
            'is_transfer' => false,
            'is_card' => false,
            'settings' => json_encode([
                'name' => 'Efectivo',
                'default' => true,
                'icon' => 'fas fa-cash-register',
                'employee' => false
            ])
        ]);

        PaymentMethod::create([
            'name' => 'Descuento en libranza.',
            'is_cash' => false,
            'is_libranza' => true,
            'is_transfer' => false,
            'is_card' => false,
            'settings' => json_encode([
                'name' => 'Libranza',
                'default' => false,
                'icon' => 'fas fa-receipt',
                'path' => '',
                'employee' => true
            ])
        ]);

        PaymentMethod::create([
            'name' => 'Transferencia Nequi.',
            'is_cash' => false,
            'is_libranza' => false,
            'is_transfer' => true,
            'is_card' => false,
            'settings' => json_encode([
                'name' => 'Nequi',
                'default' => false,
                'icon' => 'fas fa-credit-card',
                'path' => '',
                'verify' => true,
                'auth' => (object) [
                    'name' => 'Autorizacion',
                    'description' => 'Servicio de Autorización OAuth2',
                    'path' => '/token',
                ],
                'options' => [
                    (object) [
                        'name' => 'Noti Push',
                        'description' => 'Notificacion de pago directo al nequi de la persona.',
                        'default' => true,
                        'settings' => (object) [
                            'generate' => (object) [
                                'url' => '',
                                'path' => '/-services-paymentservice-unregisteredpayment',
                                'description' => 'Enviar notificación push.'
                            ],
                            'cancel' => (object) [
                                'url' => '',
                                'path' => '/-services-paymentservice-cancelunregisteredpayment',
                                'description' => 'Cancelar notificación push de un pago pendiente.'
                            ],
                            'status' => (object) [
                                'url' => '',
                                'path' => '/-services-paymentservice-getstatuspayment',
                                'description' => 'Consultar estado de pago.'
                            ],
                            'reverse' => (object) [
                                'url' => '',
                                'path' => '/-services-reverseservices-reversetransaction',
                                'description' => 'Reversar transacción.'
                            ],
                        ]
                    ],
                    (object) [
                        'name' => 'Codigo QR',
                        'description' => 'Escaneo de codigo qr desde la aplicacion.',
                        'default' => false,
                        'settings' => (object) [
                            'generate' => (object) [
                                'url' => '',
                                'path' => '/-services-paymentservice-generatecodeqr',
                                'description' => 'Generar código.'
                            ],
                            'status' => (object) [
                                'url' => '',
                                'path' => '/-services-paymentservice-getstatuspayment',
                                'description' => 'Consultar estado de pago.'
                            ],
                            'reverse' => (object) [
                                'url' => '',
                                'path' => '/-services-reverseservices-reversetransaction',
                                'description' => 'Reversar transacción.'
                            ],
                        ]
                    ]
                ]
            ])
        ]);
    }
}
