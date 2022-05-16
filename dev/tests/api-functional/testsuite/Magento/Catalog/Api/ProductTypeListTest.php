<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductTypeListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductTypeListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testGetProductTypes()
    {
        $expectedProductTypes = [
            [
                'name' => 'simple',
                'label' => 'Simple Products',
            ],
            [
                'name' => 'virtual',
                'label' => 'Virtual Products',
            ],
            [
                'name' => 'downloadable',
                'label' => 'Downloadable Products',
            ],
            [
                'name' => 'bundle',
                'label' => 'Bundle Products',
            ],
            [
                'name' => 'configurable',
                'label' => 'Configurable Products',
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/types',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetProductTypes',
            ],
        ];

        $productTypes = $this->_webApiCall($serviceInfo);

        foreach ($expectedProductTypes as $expectedProductType) {
            $this->assertContains($expectedProductType, $productTypes);
        }
    }
}
