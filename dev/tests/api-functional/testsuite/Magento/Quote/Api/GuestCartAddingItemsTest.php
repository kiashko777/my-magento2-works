<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class for testing adding and deleting items flow.
 */
class GuestCartAddingItemsTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'quoteGuestCartManagementV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Test price for cart after deleting and adding product to.
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_without_options_with_stock_data.php
     * @return void
     */
    public function testPriceForCreatingQuoteFromEmptyCart()
    {
        // Creating empty cart
        $serviceInfoForCreatingEmptyCart = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateEmptyCart',
            ],
        ];
        $quoteId = $this->_webApiCall($serviceInfoForCreatingEmptyCart);

        // Adding item to the cart
        $serviceInfoForAddingProduct = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $quoteId . '/items',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => GuestCartItemRepositoryTest::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => GuestCartItemRepositoryTest::SERVICE_NAME . 'Save',
            ],
        ];
        $requestData = [
            'cartItem' => [
                'quote_id' => $quoteId,
                'sku' => 'simple',
                'qty' => 1
            ]
        ];
        $item = $this->_webApiCall($serviceInfoForAddingProduct, $requestData);
        $this->assertNotEmpty($item);

        // Delete the item for the cart
        $serviceInfoForDeleteProduct = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $quoteId . '/items/' . $item['item_id'],
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => GuestCartItemRepositoryTest::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => GuestCartItemRepositoryTest::SERVICE_NAME . 'deleteById',
            ],
        ];
        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfoForDeleteProduct, ['cartId' => $quoteId, 'itemId' => $item['item_id']])
            : $this->_webApiCall($serviceInfoForDeleteProduct);
        $this->assertTrue($response);

        // Add one more item and check price for this item
        $serviceInfoForAddingProduct = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $quoteId . '/items',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => GuestCartItemRepositoryTest::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => GuestCartItemRepositoryTest::SERVICE_NAME . 'Save',
            ],
        ];
        $requestData = [
            'cartItem' => [
                'quote_id' => $quoteId,
                'sku' => 'simple',
                'qty' => 1
            ]
        ];
        $item = $this->_webApiCall($serviceInfoForAddingProduct, $requestData);
        $this->assertNotEmpty($item);
        $this->assertEquals($item['price'], 10);

        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load($quoteId);
        $quote->delete();
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
