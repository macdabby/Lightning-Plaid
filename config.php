<?php

return [
    'routes' => [
        'static' => [
            'api/plaid/connect' => 'Modules\\Plaid\\API\\Connect',
        ]
    ],
    'js' => [
        // Module Name
        'Plaid' => [
            // Source file => Dest file
            'Plaid.js' => 'Checkout.min.js',
        ]
    ],
];
