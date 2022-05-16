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
            'customerAdded' => [
                'name' => 'customerAdded',
                'is_synchronous' => false,
                'request' => CustomerInterface::class,
                'request_type' => 'object_interface',
                'response' => null,
                'handlers' => [
                    'customerCreatedFirst' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'invalid',
                    ],
                    'customerCreatedSecond' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'delete',
                    ],
                    'saveNameNotDisabled' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                    'saveNameNotDisabledDigit' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                ],
            ],
        ]
    ]
];
