<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\Data\CustomerInterface;

return [
    'topics' => [
        'customer.created' => [
            'request_type' => 'object_interface',
            'request' => CustomerInterface::class,
            'response' => null,
        ],
        'customer.list.retrieved' => [
            'request_type' => 'object_interface',
            'request' => 'Magento\Customer\Api\Data\CustomerInterface[]',
            'response' => null,
        ],
        'customer.updated' => [
            'request_type' => 'object_interface',
            'request' => CustomerInterface::class,
            'response' => null,
        ],
        'customer.deleted' => [
            'request_type' => 'object_interface',
            'request' => CustomerInterface::class,
            'response' => null,
        ],
        'product.created' => [
            'request_type' => 'object_interface',
            'request' => ProductInterface::class,
            'response' => null,
        ],
    ],
];
