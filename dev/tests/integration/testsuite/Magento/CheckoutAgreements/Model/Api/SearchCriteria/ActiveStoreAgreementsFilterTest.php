<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CheckoutAgreements\Model\Api\SearchCriteria;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ActiveStoreAgreementsFilterTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ActiveStoreAgreementsFilter
     */
    private $model;

    public function testBuildSearchCriteria()
    {
        $expected = [
            'filter_groups' => [
                [
                    'filters' => [
                        [
                            'field' => 'store_id',
                            'condition_type' => 'eq',
                            'value' => 1,
                        ]
                    ]
                ],
                [
                    'filters' => [
                        [
                            'field' => 'is_active',
                            'condition_type' => 'eq',
                            'value' => 1,
                        ]
                    ]
                ],
            ]
        ];
        $searchCriteria = $this->model->buildSearchCriteria();
        $this->assertEquals($expected, $searchCriteria->__toArray());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            ActiveStoreAgreementsFilter::class
        );
    }
}
