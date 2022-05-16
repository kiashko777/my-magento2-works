<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProduct\Api;

use Magento\Catalog\Model\Product;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class CartItemRepositoryTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteCartItemRepositoryV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     * @magentoApiDataFixture Magento/GroupedProduct/_files/product_grouped_with_simple_out_of_stock.php
     */
    public function testAddGroupedProductToCartThatHasAnOutOfStockItemInTheGroup()
    {
        $this->_markTestAsRestOnly();

        /** @var  Product $product */
        $product = $this->objectManager->create(Product::class)->load('100000003');
        $productSku = $product->getSku();
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/items',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        $requestData = [
            'cartItem' => [
                'sku' => $productSku,
                'qty' => 1,
                'quote_id' => $cartId,
            ],
        ];
        $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($quote->hasProductId('100000001'));
        $this->assertFalse($quote->hasProductId('100000002'));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
