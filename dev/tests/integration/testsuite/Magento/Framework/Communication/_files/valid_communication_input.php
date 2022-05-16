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
            'customerCreated' => [
                'name' => 'customerCreated',
                'is_synchronous' => true,
                'request' => CustomerInterface::class,
                'request_type' => 'object_interface',
                'response' => CustomerInterface::class,
                'handlers' => [
                    'default' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                ],
            ],
            'customerAdded' => [
                'name' => 'customerAdded',
                'is_synchronous' => false,
                'request' => 'string[]',
                'request_type' => 'object_interface',
                'response' => null,
                'handlers' => [
                    'customerCreatedFirst' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                    'customerCreatedSecond' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'delete',
                    ],
                    'customerCreatedExtra' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
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
            'customerUpdated' => [
                'name' => 'customerUpdated',
                'is_synchronous' => true,
                'request' => CustomerInterface::class,
                'request_type' => 'object_interface',
                'response' => 'Magento\Customer\Api\Data\CustomerInterface[]',
                'handlers' => [
                    'updateName' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                ],
            ],
            'customerModified' => [
                'name' => 'customerModified',
                'is_synchronous' => false,
                'request' => CustomerInterface::class,
                'request_type' => 'object_interface',
                'response' => null,
                'handlers' => [
                    'updateName' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'save',
                    ],
                ],
            ],
            'customerRetrieved' => [
                'name' => 'customerRetrieved',
                'is_synchronous' => true,
                'request' => [
                    [
                        'param_name' => 'email',
                        'param_position' => 0,
                        'is_required' => true,
                        'param_type' => 'string',
                    ],
                    [
                        'param_name' => 'websiteId',
                        'param_position' => 1,
                        'is_required' => false,
                        'param_type' => 'int',
                    ],
                ],
                'request_type' => 'service_method_interface',
                'response' => CustomerInterface::class,
                'handlers' => [
                    'defaultHandler' => [
                        'type' => CustomerRepositoryInterface::class,
                        'method' => 'get',
                    ],
                ],
            ],
            'customerDeleted' => [
                'name' => 'customerDeleted',
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
        ],
    ]
];
