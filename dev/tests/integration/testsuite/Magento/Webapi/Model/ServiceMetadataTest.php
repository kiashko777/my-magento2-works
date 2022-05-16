<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ServiceMetadataTest extends TestCase
{
    /**
     * @var ServiceMetadata
     */
    private $serviceMetadata;

    public function testGetServiceMetadata()
    {
        $expected = [
            'methods' => [
                'activate' => [
                    'method' => 'activate',
                    'inputRequired' => false,
                    'isSecure' => false,
                    'resources' => [
                        'Magento_Customer::manage'
                    ],
                    'documentation' => 'Activate a customer account using a key that was sent in a confirmation email.',
                    'interface' => [
                        'in' => [
                            'parameters' => [
                                'email' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'documentation' => null
                                ],
                                'confirmationKey' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'documentation' => null
                                ]
                            ]
                        ],
                        'out' => [
                            'parameters' => [
                                'result' => [
                                    'type' => 'CustomerDataCustomerInterface',
                                    'required' => true,
                                    'documentation' => ''
                                ]
                            ],
                            'throws' => [
                                '\\' . LocalizedException::class
                            ]
                        ]
                    ]
                ]
            ],
            'class' => AccountManagementInterface::class,
            'description' => 'Interface for managing customers accounts.',
        ];
        $actual = $this->serviceMetadata->getServiceMetadata('customerAccountManagementV1');
        $this->assertEquals(array_replace_recursive($actual, $expected), $actual);
    }

    public function testGetRouteMetadata()
    {
        $expected = [
            'methods' => [
                'activate' => [
                    'method' => 'activate',
                    'inputRequired' => false,
                    'isSecure' => false,
                    'resources' => [
                        'Magento_Customer::manage'
                    ],
                    'documentation' => 'Activate a customer account using a key that was sent in a confirmation email.',
                    'interface' => [
                        'in' => [
                            'parameters' => [
                                'email' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'documentation' => null
                                ],
                                'confirmationKey' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'documentation' => null
                                ]
                            ]
                        ],
                        'out' => [
                            'parameters' => [
                                'result' => [
                                    'type' => 'CustomerDataCustomerInterface',
                                    'required' => true,
                                    'documentation' => ''
                                ]
                            ],
                            'throws' => [
                                '\\' . LocalizedException::class
                            ]
                        ]
                    ]
                ]
            ],
            'class' => AccountManagementInterface::class,
            'description' => 'Interface for managing customers accounts.',
            'routes' => [
                '/V1/customers/me/activate' => [
                    'PUT' => [
                        'method' => 'activateById',
                        'parameters' => [
                            'customerId' => [
                                'force' => true,
                                'value' => '%customer_id%'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $actual = $this->serviceMetadata->getRouteMetadata('customerAccountManagementV1');
        $this->assertEquals(array_replace_recursive($actual, $expected), $actual);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->serviceMetadata = $objectManager->create(ServiceMetadata::class);
    }
}
