<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GuestShippingMethodManagementTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteGuestShippingMethodManagementV1';
    const RESOURCE_PATH = '/V1/guest-carts/';
    /**
     * @var Quote
     */
    protected $quote;
    /**
     * @var TotalsCollector
     */
    protected $totalsCollector;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     *
     */
    public function testGetListForVirtualCart()
    {
        $quote = $this->objectManager->create(Quote::class);
        $cartId = $quote->load('test_order_with_virtual_product', 'reserved_order_id')->getId();

        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $this->assertEquals([], $this->_webApiCall($this->getListServiceInfo($cartId), ["cartId" => $cartId]));
    }

    /**
     * Service info
     *
     * @param int $cartId
     * @return array
     */
    protected function getListServiceInfo($cartId)
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/shipping-methods',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
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
        if (!$cartId) {
            $this->fail('quote fixture failed');
        }

        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $quote->getShippingAddress()->collectShippingRates();
        $expectedRates = $quote->getShippingAddress()->getGroupedAllShippingRates();

        $expectedData = $this->convertRates($expectedRates, $quote->getQuoteCurrencyCode());

        $requestData = ["cartId" => $cartId];

        $returnedRates = $this->_webApiCall($this->getListServiceInfo($cartId), $requestData);
        $this->assertEquals($expectedData, $returnedRates);
    }

    /**
     * Convert rate models array to data array
     *
     * @param string $currencyCode
     * @param Rate[] $groupedRates
     * @return array
     */
    protected function convertRates($groupedRates, $currencyCode)
    {
        $result = [];
        /** @var ShippingMethodConverter $converter */
        $converter = $this->objectManager->create(ShippingMethodConverter::class);
        foreach ($groupedRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $result[] = $converter->modelToDataObject($rate, $currencyCode)->__toArray();
            }
        }
        return $result;
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->quote = $this->objectManager->create(Quote::class);
        $this->totalsCollector = $this->objectManager->create(TotalsCollector::class);
    }
}
