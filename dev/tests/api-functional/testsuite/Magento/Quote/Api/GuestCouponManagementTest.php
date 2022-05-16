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
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GuestCouponManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteGuestCouponManagementV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

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

        $cartId = $this->getQuoteMaskedId($quote->getId());

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

    protected function getQuoteMaskedId($quoteId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->objectManager->create(QuoteIdMaskFactory::class)->create();
        $quoteIdMask->load($quoteId, 'quote_id');
        return $quoteIdMask->getMaskedId();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_coupon_saved.php
     */
    public function testDelete()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $this->getQuoteMaskedId($quote->getId());
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
        $cartId = $this->getQuoteMaskedId($quote->getId());

        $couponCode = 'invalid_coupon_code';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons/' . $couponCode,
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
        $cartId = $this->getQuoteMaskedId($quote->getId());
        $salesRule = $this->objectManager->create(Rule::class);
        $salesRuleId = $this->objectManager->get(Registry::class)
            ->registry('Magento/Checkout/_file/discount_10percent');
        $salesRule->load($salesRuleId);
        $couponCode = $salesRule->getPrimaryCoupon()->getCode();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/coupons/' . $couponCode,
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

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    protected function tearDown(): void
    {
        $createdQuotes = ['test_order_1', 'test01'];
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        foreach ($createdQuotes as $quoteId) {
            $quote->load($quoteId, 'reserved_order_id');
            $quote->delete();
            /** @var QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->objectManager->create(QuoteIdMask::class);
            $quoteIdMask->load($quoteId, 'quote_id');
            $quoteIdMask->delete();
        }
    }
}
