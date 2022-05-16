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
                        'method' => 'save',
                        'disabled' => false
                    ],
                ],
            ],
            'customerCreated' => [
                'name' => 'customerCreated',
                'is_synchronous' => false,
                'request' => CustomerInterface::class,
                'request_type' => 'object_interface',
                'response' => null,
                'handlers' => [
                    'default' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                        'disabled' => true
                    ],
                ],
            ],
        ]
    ]
];
