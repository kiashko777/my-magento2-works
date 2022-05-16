<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

return [
    'communication' => [
        'topics' => [
            'customerDeleted' => [
                'name' => 'customerRemoved',
                'is_synchronous' => true,
                'request' => [
                    [
                        'param_name' => 'customer',
                        'param_position' => 0,
                        'is_required' => true,
                        'param_type' => CustomerInterface::class,
                    ],
                ],
                'request_type' => 'service_method_interface',
                'response' => 'bool',
                'handlers' => [
                    'customHandler' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'deleteById',
                    ],
                ],
            ],
        ]
    ]
];
