<?php

return [
    'checkout' => [
        'store' => [
            'driver' => env('VANILO_CHECKOUT_DRIVER', 'session'),
        ],
        'default' => [
            'weight_unit' => 'kg',
        ],
    ],
];
