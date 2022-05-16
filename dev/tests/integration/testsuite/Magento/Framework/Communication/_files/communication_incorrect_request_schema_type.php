<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\Data\CustomerInterface;

return [
    'communication' => [
        'topics' => [
            'customerCreated' => [
                'name' => 'customerCreated',
                'is_synchronous' => false,
                'request' => CustomerInterface::class,
                'request_type' => 'incorrect',
                'response' => null,
                'handlers' => [],
            ],
        ]
    ]
];
