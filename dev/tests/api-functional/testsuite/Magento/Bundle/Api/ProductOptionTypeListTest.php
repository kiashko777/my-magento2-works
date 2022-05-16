<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductOptionTypeListTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'bundleProductOptionTypeListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/options/types';

    public function testGetTypes()
    {
        $expected = [
            ['label' => 'Drop-down', 'code' => 'select'],
            ['label' => 'Radio Buttons', 'code' => 'radio'],
            ['label' => 'Checkbox', 'code' => 'checkbox'],
            ['label' => 'Multiple Select', 'code' => 'multi'],
        ];
        $result = $this->getTypes();

        $this->assertEquals($expected, $result);
    }

    /**
     * @return string
     */
    protected function getTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getItems',
            ],
        ];
        return $this->_webApiCall($serviceInfo);
    }
}
