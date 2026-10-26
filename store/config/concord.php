<?php

return [
    'modules' => [
        Konekt\AppShell\Providers\ModuleServiceProvider::class => [
            'ui' => [
                'name' => 'فروشگاه من',
                'url'  => '/admin/product',
            ],
        ],
        Vanilo\Foundation\Providers\ModuleServiceProvider::class => [
            'image' => [
                'product' => [
                    'variants' => [
                        'thumbnail' => [
                            'width'  => 400,
                            'height' => 400,
                            'fit'    => 'contain',
                        ],
                        'medium' => [
                            'width'  => 800,
                            'height' => 800,
                            'fit'    => 'contain',
                        ],
                    ],
                ],
            ],
        ],
        Vanilo\Admin\Providers\ModuleServiceProvider::class,
    ],
    'register_route_models' => true,
];
