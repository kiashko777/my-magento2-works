<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\Data\InvalidInterface;

return [
    'communication' => [
        'topics' => [
            'customerCreated' => [
                'name' => 'customerCreated',
                'is_synchronous' => false,
                'request' => InvalidInterface::class,
                'request_type' => 'object_interface',
                'response' => null,
                'handlers' => [],
            ],
        ]
    ]
];
