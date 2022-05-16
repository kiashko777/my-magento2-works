<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Routing;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModule5\Service\V1\Entity\AllSoapAndRest;
use Magento\TestModule5\Service\V1\Entity\AllSoapAndRestFactory;

/**
 * Class to test overriding request body identifier property with id passed in url path parameter
 *
 * Refer to \Magento\Framework\Webapi\Rest\Request::overrideRequestBodyIdWithPathParam
 */
class RequestIdOverrideTest extends BaseService
{
    /**
     * @var string
     */
    protected $_version;

    /**
     * @var string
     */
    protected $_restResourcePath;

    /**
     * @var AllSoapAndRestFactory
     */
    protected $itemFactory;

    /**
     * @var string
     */
    protected $_soapService = 'testModule5AllSoapAndRest';

    public function testOverride()
    {
        $itemId = 1;
        $incorrectItemId = 2;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $item = $this->itemFactory->create()
            ->setEntityId($incorrectItemId)
            ->setName('test');
        $requestData = ['entityItem' => $item->__toArray()];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $itemId,
            $item[AllSoapAndRest::ID],
            'Identifier overriding failed.'
        );
    }

    public function testOverrideNested()
    {
        $firstItemId = 1;
        $secondItemId = 11;
        $incorrectItemId = 2;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $firstItemId . '/nestedResource/' . $secondItemId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $item = $this->itemFactory->create()
            ->setEntityId($incorrectItemId)
            ->setName('test');
        $requestData = ['entityItem' => $item->__toArray()];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $secondItemId,
            $item[AllSoapAndRest::ID],
            'Identifier overriding failed for nested resource request.'
        );
    }

    public function testOverrideAdd()
    {
        $itemId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $item = $this->itemFactory->create()
            ->setName('test');
        $requestData = ['entityItem' => $item->__toArray()];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $itemId,
            $item[AllSoapAndRest::ID],
            'Identifier replacing failed.'
        );
    }

    /**
     * Test if the framework works if camelCase path parameters are provided instead of valid snake case ones.
     * Webapi Framework currently accepts both cases due to shortcoming in Serialization (MAGETWO-29833).
     * Unless it is fixed this use case is valid.
     */
    public function testAddCaseMismatch()
    {
        $itemId = 1;
        $incorrectItemId = 2;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];
        $requestData = ['entityItem' => ['entityId' => $incorrectItemId, 'name' => 'test']];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $itemId,
            $item[AllSoapAndRest::ID],
            'Identifier overriding failed.'
        );
    }

    public function testOverrideWithScalarValues()
    {
        $firstItemId = 1;
        $secondItemId = 11;
        $incorrectItemId = 2;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/{$this->_version}/TestModule5/OverrideService/" . $firstItemId
                    . '/nestedResource/' . $secondItemId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
        ];

        $requestData = ['entity_id' => $incorrectItemId, 'name' => 'test', 'orders' => true];
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $secondItemId,
            $item[AllSoapAndRest::ID],
            'Identifier overriding failed.'
        );
    }

    protected function setUp(): void
    {
        $this->_markTestAsRestOnly('Request Id overriding is a REST based feature.');
        $this->_version = 'V1';
        $this->_restResourcePath = "/{$this->_version}/TestModule5/";
        $this->itemFactory = Bootstrap::getObjectManager()
            ->create(AllSoapAndRestFactory::class);
    }
}
