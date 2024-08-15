<?php

namespace App\Traits;

trait Trademark
{
	private function trademark($string) : string
    {
        $codeMayuscula = strtoupper($string);
    
        $mappingCodeTrademark = [
            '1' => 'ZARETH PREMIUM',
            '2' => 'STARA GIRLS',
            '3' => 'STARA',
            '4' => 'ZARETH TEENS',
            '5' => 'BLESS',
            '6' => 'BLESS 23 JUNIOR',
            '7' => 'ZARETH',
            '8' => 'BLESS 23',
            '9' => 'SHIREL',
            'H' => 'STARA MEN',
            'E' => function($code) {
                if (substr($code, 0, 2) == 'EL') {
                    return 'ELOHE';
                } else {
                    return 'ELOHE';
                }
            },
            'S' => function($code) {
                if (substr($code, 0, 3) == 'STG') {
                    return 'STARA BLUSAS';
                } else if (substr($code, 0, 3) == 'STD') {
                    return 'STARA BOYFRIEND';
                } else {
                    return 'SIN DEFINIR';
                }
            },
            'M' => function($code) {
                if (substr($code, 0, 3) == 'MVP') {
                    return 'MICHELL VILLAMIZAR PLUS';
                } else if (substr($code, 0, 2) == 'MC') {
                    return 'MICHELL MEN';
                } else if (substr($code, 0, 2) == 'MK') {
                    return 'MICHELL KIDS';
                } else if (substr($code, 0, 2) == 'MV') {
                    return 'MICHELL VILLAMIZAR';
                } else {
                    return 'MICHELL';
                }
            },
            'F' => function($code) {
                if (substr($code, 0, 2) == 'FV') {
                    return 'FIANCHI VIP';
                } else if (substr($code, 0, 2) == 'FR') {
                    return 'FARFALLA';
                } else {
                    return 'FLOW';
                }
            },
            'B' => function($code) {
                if (substr($code, 0, 2) == 'BZ') {
                    return 'ESTILOS BZ';
                } else {
                    return 'ZARETH CURVE PLUS';
                }
            },
            'C' => function($code) {
                if (substr($code, 0, 3) == 'CRP' || substr($code, 0, 3) == 'CPP') {
                    return 'CALIFORNIA PLUS';
                } else if (substr($code, 0, 2) == 'CR') {
                    return 'CALIFORNIA';
                } else if (substr($code, 0, 2) == 'CM') {
                    return 'CALIFORNIA MEN';
                } else if (substr($code, 0, 2) == 'CK') {
                    return 'CALIFORNIA KIDS';
                } else if (substr($code, 0, 2) == 'CT') {
                    return 'CALIFORNIA TEENS';
                } else if (substr($code, 0, 2) == 'CP') {
                    return 'CALIFORNIA PREMIUM';
                } else if (substr($code, 0, 2) == 'CV') {
                    return 'CURVE LOS ANGELES';
                } else if (substr($code, 0, 2) == 'C9') {
                    return 'SHIREL CLASIC';
                } else {
                    return 'SIN DEFINIR';
                }
            },
            'L' => function($code) {
                if (substr($code, 0, 2) == 'LR') {
                    return 'LOA RIGIDO';
                } else if (substr($code, 0, 2) == 'LS') {
                    return 'LOA STRECH';
                } else {
                    return 'LOA';
                }
            },
            'N' => function($code) {
                if (substr($code, 0, 2) == 'NY') {
                    return 'NEW YORK';
                } else if (substr($code, 0, 2) == 'NE') {
                    return 'NEON CAMISA';
                } else if (substr($code, 0, 2) == 'NK') {
                    return 'NEON KIDS';
                } else if (substr($code, 0, 2) == 'NB') {
                    return 'NEON CAMISA NIÑA';
                } else {
                    return 'NEON';
                }
            },
            'Y' => function($code) {
                if (substr($code, 0, 2) == 'YD') {
                    return 'NEW YORK';
                } else if (substr($code, 0, 2) == 'YB' || substr($code, 0, 2) == 'YG') {
                    return 'NEW YORK PLUS';
                } else if (substr($code, 0, 2) == 'YM') {
                    return 'NEW YORK MEN';
                } else if (substr($code, 0, 2) == 'YK') {
                    return 'NEW YORK KIDS';
                } else if (substr($code, 0, 2) == 'YT') {
                    return 'NEW YORK TEENS';
                } else {
                    return 'NEW YORK';
                }
            },
            'D' => 'DHARA',
            'Z' => 'STORE',
            'K' => 'STARA KIDS',
            'P' => 'ZARETH PREMIUM',
            'A' => 'ALPHA LEGACY',
            'O' => 'BLESS ORIGINAL'
        ];
    
        $firstChart = $codeMayuscula[0];
    
        if (array_key_exists($firstChart, $mappingCodeTrademark)) {
            $trademark = $mappingCodeTrademark[$firstChart];
    
            if (is_callable($trademark)) {
                $trademark = $trademark($codeMayuscula);
            }
    
            return $trademark;
        } else {
            return 'SIN DEFINIR';
        }
    }

    private function trademarks() : array
    {
        return [
            'ALPHA LEGACY' => [
                'DATA' => [],
                'IMG' => '',
                'TRADEMARKS' => [
                    'ALPHA LEGACY' => [
                        'DATA' => [],
                        'IMG' => ''
                    ]
                ]
            ],
            'BLESS' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/BLESS.png',
                'TRADEMARKS' => [
                    'BLESS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/BLESS.png'
                    ],
                    'BLESS 23' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/BLESS 23.png'
                    ],
                    'BLESS 23 JUNIOR' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/BLESS 23.png'
                    ],
                    'BLESS ORIGINAL' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/BLESS ORIGINAL.png'
                    ]
                ]
            ],
            'CALIFORNIA' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/CALIFORNIA.png',
                'TRADEMARKS' => [
                    'CALIFORNIA' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'CALIFORNIA KIDS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'CALIFORNIA MEN' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'CALIFORNIA PLUS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'CALIFORNIA PREMIUM' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'CALIFORNIA TEENS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ]
                ]
            ],
            'SHIREL' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/SHIREL.png',
                'TRADEMARKS' => [
                    'SHIREL' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL.png'
                    ],
                    'SHIREL CLASIC' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/SHIREL CLASIC.png'
                    ]
                ]
            ],
            'CURVE LOS ANGELES' => [
                'IMG' => 'images/Trademarks/CURVE LOS ANGELES.png',
                'TRADEMARKS' => [
                    'CURVE LOS ANGELES' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/CURVE LOS ANGELES.png'
                    ]
                ]
            ],
            'DHARA' => [
                'IMG' => 'images/Trademarks/DHARA.png',
                'TRADEMARKS' => [
                    'DHARA' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/DHARA.png'
                    ]
                ]
            ],
            'ELOHE' => [
                'IMG' => 'images/Trademarks/ELOHE.png',
                'TRADEMARKS' => [
                    'ELOHE' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ELOHE.png'
                    ]
                ]
            ],
            'ESTILOS BZ' => [
                'IMG' => 'images/Trademarks/ESTILOS BZ.png',
                'TRADEMARKS' => [
                    'ESTILOS BZ' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ESTILOS BZ.png'
                    ]
                ]
            ],
            'FARFALLA' => [
                'IMG' => 'images/Trademarks/FARFALLA.png',
                'TRADEMARKS' => [
                    'FARFALLA' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/FARFALLA.png'
                    ]
                ]
            ],
            'FIANCHI VIP' => [
                'IMG' => 'images/Trademarks/FIANCHI VIP.png',
                'TRADEMARKS' => [
                    'FIANCHI VIP' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/FIANCHI VIP.png'
                    ]
                ]
            ],
            'FLOW' => [
                'IMG' => 'images/Trademarks/FLOW.png',
                'TRADEMARKS' => [
                    'FLOW' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/FLOW.png'
                    ]
                ]
            ],
            'LOA' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/LOA.png',
                'TRADEMARKS' => [
                    'LOA' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/LOA.png'
                    ],
                    'LOA RIGIDO' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'LOA STRECH' => [
                        'DATA' => [],
                        'IMG' => ''
                    ]
                ]
            ],
            'MICHELL' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/MICHELL.png',
                'TRADEMARKS' => [
                    'MICHELL' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/MICHELL.png'
                    ],
                    'MICHELL KIDS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'MICHELL MEN' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'MICHELL VILLAMIZAR' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/MICHELL VILLAMIZAR.png'
                    ],
                    'MICHELL VILLAMIZAR PLUS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ]
                ]
            ],
            'NEON' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/NEON.png',
                'TRADEMARKS' => [
                    'NEON' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/NEON.png'
                    ],
                    'NEON CAMISA' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'NEON CAMISA NIÑA' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'NEON KIDS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ]
                ]
            ],
            'NEW YORK' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/NEW YORK.png',
                'TRADEMARKS' => [
                    'NEW YORK' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/NEW YORK.png'
                    ],
                    'NEW YORK KIDS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'NEW YORK MEN' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'NEW YORK PLUS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'NEW YORK TEENS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ]
                ]
            ],
            'STARA' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/STARA.png',
                'TRADEMARKS' => [
                    'STARA' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/STARA.png'
                    ],
                    'STARA BLUSAS' => [
                        'DATA' => [],
                        'IMG' => ''
                    ],
                    'STARA GIRLS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/STARA GIRLS.png'
                    ],
                    'STARA KIDS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/STARA KIDS.png'
                    ],
                    'STARA MEN' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/STARA MEN.png'
                    ]
                ]
            ],
            'STORE' => [
                'DATA' => [],
                'IMG' => '',
                'TRADEMARKS' => [
                    'STORE' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/STORE.png'
                    ]
                ]
            ],
            'ZARETH' => [
                'DATA' => [],
                'IMG' => 'images/Trademarks/ZARETH.png',
                'TRADEMARKS' => [
                    'ZARETH' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ZARETH.png'
                    ],
                    'ZARETH CURVE PLUS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ZARETH CURVE PLUS.png'
                    ],
                    'ZARETH PREMIUM' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ZARETH PREMIUM.png'
                    ],
                    'ZARETH TEENS' => [
                        'DATA' => [],
                        'IMG' => 'images/Trademarks/ZARETH TEENS.png'
                    ]
                ]
            ]
        ];
    }
}
