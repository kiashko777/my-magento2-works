<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Api;

use Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class PaymentMethodManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quotePaymentMethodManagementV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_payment_saved.php
     */
    public function testReSetPayment()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1_with_payment', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "method" => [
                'method' => 'checkmo',
                'po_number' => null
            ],
        ];

        $this->assertNotNull($this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     */
    public function testSetPaymentWithVirtualProduct()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_with_virtual_product', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "method" => [
                'method' => 'checkmo',
                'po_number' => '200'
            ],
        ];
        $this->assertNotNull($this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetPaymentWithSimpleProduct()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "method" => [
                'method' => 'checkmo',
                'po_number' => '200'
            ],
        ];

        $this->assertNotNull($this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_simple_product_saved.php
     */
    public function testSetPaymentWithSimpleProductWithoutAddress()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The shipping address is missing. Set the address and try again.');

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_with_simple_product_without_address', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "method" => [
                'method' => 'checkmo',
                'po_number' => '200'
            ],
        ];
        $this->assertNotNull($this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetList()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/payment-methods',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList',
            ],
        ];

        $requestData = ["cartId" => $cartId];
        $requestResponse = $this->_webApiCall($serviceInfo, $requestData);

        $expectedResponse = [
            'code' => 'checkmo',
            'title' => 'Check / Money order',
        ];

        $this->assertGreaterThan(0, count($requestResponse));
        $this->assertContains($expectedResponse, $requestResponse);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_payment_saved.php
     */
    public function testGet()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1_with_payment', 'reserved_order_id');
        $cartId = $quote->getId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get',
            ],
        ];

        $requestData = ["cartId" => $cartId];
        $requestResponse = $this->_webApiCall($serviceInfo, $requestData);

        foreach ($this->getPaymentMethodFieldsForAssert() as $field) {
            $this->assertArrayHasKey($field, $requestResponse);
            $this->assertNotNull($requestResponse[$field]);
        }

        $this->assertEquals('checkmo', $requestResponse['method']);
    }

    /**
     * @return array
     */
    protected function getPaymentMethodFieldsForAssert()
    {
        return ['method', 'po_number', 'additional_data'];
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetListMine()
    {
        $this->_markTestAsRestOnly();

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/payment-methods',
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $this->getCustomerToken()
            ]
        ];

        $requestResponse = $this->_webApiCall($serviceInfo);

        $expectedResponse = [
            'code' => 'checkmo',
            'title' => 'Check / Money order',
        ];

        $this->assertGreaterThan(0, count($requestResponse));
        $this->assertContains($expectedResponse, $requestResponse);
    }

    /**
     * Get customer ID token
     *
     * @return string
     */
    protected function getCustomerToken()
    {
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');
        return $token;
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_payment_saved.php
     */
    public function testGetMine()
    {
        $this->_markTestAsRestOnly();

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1_with_payment', 'reserved_order_id');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $this->getCustomerToken()
            ]
        ];

        $requestResponse = $this->_webApiCall($serviceInfo);

        foreach ($this->getPaymentMethodFieldsForAssert() as $field) {
            $this->assertArrayHasKey($field, $requestResponse);
            $this->assertNotNull($requestResponse[$field]);
        }
        $this->assertEquals('checkmo', $requestResponse['method']);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetPaymentWithSimpleProductMine()
    {
        $this->_markTestAsRestOnly();

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/selected-payment-method',
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $this->getCustomerToken()
            ]
        ];

        $requestData = [
            "method" => [
                'method' => 'checkmo',
                'po_number' => '200'
            ],
        ];

        $this->assertNotNull($this->_webApiCall($serviceInfo, $requestData));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
