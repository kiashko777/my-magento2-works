<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;
use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductLinkTypeListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkTypeListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testGetItems()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'links/types',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetItems',
            ],
        ];

        $actual = $this->_webApiCall($serviceInfo);

        /**
         * Validate that product type links provided by Magento_GroupedProduct module are present
         */
        $expectedItems = ['name' => 'associated', 'code' => Link::LINK_TYPE_GROUPED];
        $this->assertContainsEquals($expectedItems, $actual);
    }

    public function testGetItemAttributes()
    {
        $linkType = 'associated';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'links/' . $linkType . '/attributes',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetItemAttributes',
            ],
        ];

        $actual = $this->_webApiCall($serviceInfo, ['type' => $linkType]);

        $expected = [
            ['code' => 'position', 'type' => 'int'],
            ['code' => 'qty', 'type' => 'decimal'],
        ];
        $this->assertEquals($expected, $actual);
    }
}
