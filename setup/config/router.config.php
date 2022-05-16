<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Setup\Controller\Index;

return [
    'router' => [
        'routes' => [
            'literal' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Index::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
];
