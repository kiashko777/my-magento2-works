<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Api;

use Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductRepositoryMultiStoreTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';
    const STORE_CODE_FROM_FIXTURE = 'fixturestore';
    const STORE_NAME_FROM_FIXTURE = 'Fixture Store';

    private $productData = [
        [
            Product::SKU => 'simple',
            Product::NAME => 'Simple Related Products',
            Product::TYPE_ID => 'simple',
            Product::PRICE => 10
        ],
        [
            Product::SKU => 'simple_with_cross',
            Product::NAME => 'Simple Products With Related Products',
            Product::TYPE_ID => 'simple',
            Product::PRICE => 10
        ],
    ];

    /**
     * Remove test store
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()
            ->get(Registry::class);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var $store Store */
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->load('fixturestore');
        if ($store->getId()) {
            $store->delete();
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * @magentoApiDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoApiDataFixture Magento/CatalogSearch/_files/full_reindex.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetMultiStore()
    {
        $productData = $this->productData[0];
        $nameInFixtureStore = 'Name in fixture store';
        /** @var $store Group */
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->load(self::STORE_CODE_FROM_FIXTURE);
        $this->assertEquals(
            self::STORE_NAME_FROM_FIXTURE,
            $store->getName(),
            'Precondition failed: fixture store was not created.'
        );
        $sku = $productData[Product::SKU];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
        $product->load($product->getIdBySku($sku));
        $product->setName($nameInFixtureStore)->setStoreId($store->getId())->save();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $sku,
                'httpMethod' => Request::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];

        $requestData = ['id' => $sku, 'sku' => $sku];
        $defaultStoreResponse = $this->_webApiCall($serviceInfo, $requestData);
        $nameInDefaultStore = 'Simple Products';
        $this->assertEquals(
            $nameInDefaultStore,
            $defaultStoreResponse[Product::NAME],
            'Products name in default store is invalid.'
        );
        $fixtureStoreResponse = $this->_webApiCall($serviceInfo, $requestData, null, self::STORE_CODE_FROM_FIXTURE);
        $this->assertEquals(
            $nameInFixtureStore,
            $fixtureStoreResponse[Product::NAME],
            'Products name in fixture store is invalid.'
        );
    }
}
