<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Api;

use Exception;
use InvalidArgumentException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GuestPaymentMethodManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteGuestPaymentMethodManagementV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

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
        $cartId = $this->getMaskedCartId($quote->getId());

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
     * Retrieve masked cart ID for guest cart.
     *
     * @param string $cartId
     * @return string
     */
    protected function getMaskedCartId($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        return $quoteIdMask->getMaskedId();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     */
    public function testSetPaymentWithVirtualProduct()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_with_virtual_product', 'reserved_order_id');
        $cartId = $this->getMaskedCartId($quote->getId());

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
        $cartId = $this->getMaskedCartId($quote->getId());

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
        $cartId = $this->getMaskedCartId($quote->getId());

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
        $cartId = $this->getMaskedCartId($quote->getId());

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
        $cartId = $this->getMaskedCartId($quote->getId());

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

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown(): void
    {
        $this->deleteCart('test_order_1');
        $this->deleteCart('test_order_1_with_payment');
        $this->deleteCart('test_order_with_virtual_product');
        $this->deleteCart('test_order_with_virtual_product_without_address');
        parent::tearDown();
    }

    /**
     * Delete quote by given reserved order ID
     *
     * @param string $reservedOrderId
     * @return void
     */
    protected function deleteCart($reservedOrderId)
    {
        try {
            /** @var $cart Quote */
            $cart = $this->objectManager->get(Quote::class);
            $cart->load($reservedOrderId, 'reserved_order_id');
            if (!$cart->getId()) {
                throw new InvalidArgumentException('There is no quote with provided reserved order ID.');
            }
            $cart->delete();
            /** @var QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->objectManager->create(QuoteIdMask::class);
            $quoteIdMask->load($cart->getId(), 'quote_id');
            $quoteIdMask->delete();
        } catch (InvalidArgumentException $e) {
            // Do nothing if cart fixture was not used
        }
    }
}
