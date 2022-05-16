<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Api;

use Exception;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Coupon management service tests
 */
class CouponManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteCouponManagementV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_coupon_saved.php
     */
    public function testGet()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $couponCode = $quote->getCouponCode();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons/',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($couponCode, $this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_coupon_saved.php
     */
    public function testDelete()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons',
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Remove',
            ],
        ];
        $requestData = ["cartId" => $cartId];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $quote->load('test_order_1', 'reserved_order_id');
        $this->assertEquals('', $quote->getCouponCode());
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetCouponThrowsExceptionIfCouponDoesNotExist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The coupon code isn\'t valid. Verify the code and try again.');

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();

        $couponCode = 'invalid_coupon_code';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons/' . urlencode($couponCode),
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "couponCode" => $couponCode,
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Checkout/_files/discount_10percent.php
     */
    public function testSetCouponSuccess()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var Rule $salesRule */
        $salesRule = $this->objectManager->create(Rule::class);
        $salesRuleId = $this->objectManager->get(Registry::class)
            ->registry('Magento/Checkout/_file/discount_10percent');
        $salesRule->load($salesRuleId);
        $couponCode = $salesRule->getPrimaryCoupon()->getCode();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons/' . urlencode($couponCode),
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Set',
            ],
        ];

        $requestData = [
            "cartId" => $cartId,
            "couponCode" => $couponCode,
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $quoteWithCoupon = $this->objectManager->create(Quote::class);
        $quoteWithCoupon->load('test01', 'reserved_order_id');

        $this->assertEquals($quoteWithCoupon->getCouponCode(), $couponCode);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_coupon_saved.php
     */
    public function testGetMyCoupon()
    {
        $this->_markTestAsRestOnly();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $couponCode = $quote->getCouponCode();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/coupons',
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $token,
            ],
        ];

        $requestData = [];
        $this->assertEquals($couponCode, $this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_coupon_saved.php
     */
    public function testDeleteMyCoupon()
    {
        $this->_markTestAsRestOnly();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/coupons',
                'httpMethod' => Request::HTTP_METHOD_DELETE,
                'token' => $token,
            ],
        ];
        $requestData = [];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $quote->load('test_order_1', 'reserved_order_id');
        $this->assertEquals('', $quote->getCouponCode());
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetMyCouponThrowsExceptionIfCouponDoesNotExist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The coupon code isn\'t valid. Verify the code and try again.');

        $this->_markTestAsRestOnly();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        $couponCode = 'invalid_coupon_code';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/coupons/' . urlencode($couponCode),
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token,
            ],
        ];

        $requestData = [
            "couponCode" => $couponCode,
        ];

        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     * @magentoApiDataFixture Magento/Checkout/_files/discount_10percent_generalusers.php
     */
    public function testSetMyCouponSuccess()
    {
        $this->_markTestAsRestOnly();

        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $token = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var Rule $salesRule */
        $salesRule = $this->objectManager->create(Rule::class);
        $salesRuleId = $this->objectManager->get(Registry::class)
            ->registry('Magento/Checkout/_file/discount_10percent_generalusers');
        $salesRule->load($salesRuleId);
        $couponCode = $salesRule->getPrimaryCoupon()->getCode();

        /* Since this isn't a full quote fixture, need to assign it to the right customer */
        $cartManagementService = $this->objectManager->create(
            CartManagementInterface::class
        );
        $cartManagementService->assignCustomer($cartId, 1, 1);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . 'mine/coupons/' . urlencode($couponCode),
                'httpMethod' => Request::HTTP_METHOD_PUT,
                'token' => $token,
            ],
        ];

        $requestData = [
            "couponCode" => $couponCode,
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));

        $quoteWithCoupon = $this->objectManager->create(Quote::class);
        $quoteWithCoupon->load('test01', 'reserved_order_id');

        $this->assertEquals($quoteWithCoupon->getCouponCode(), $couponCode);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
