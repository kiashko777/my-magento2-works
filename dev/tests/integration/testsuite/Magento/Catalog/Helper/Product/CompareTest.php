<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Helper\Product;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Compare\ListCompare;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Catalog\Model\Session;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    /**
     * @var Compare
     */
    protected $_helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    public function testGetListUrl()
    {
        /** @var $empty Compare */
        $empty = $this->_objectManager->create(Compare::class);
        $this->assertStringContainsString('/catalog/product_compare/index/', $empty->getListUrl());
    }

    public function testGetAddUrl()
    {
        $this->_testGetProductUrl('getAddUrl', '/catalog/product_compare/add/');
    }

    protected function _testGetProductUrl($method, $expectedFullAction)
    {
        $product = $this->_objectManager->create(Product::class);
        $product->setId(10);
        $url = $this->_helper->{$method}($product);
        $this->assertStringContainsString($expectedFullAction, $url);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetAddToWishlistParams()
    {
        $product = $this->_objectManager->create(Product::class);
        $product->setId(10);
        $json = $this->_helper->getAddToWishlistParams($product);
        $params = (array)json_decode($json);
        $data = (array)$params['data'];
        $this->assertEquals('10', $data['product']);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith(
            'wishlist/index/add/',
            $params['action']
        );
    }

    public function testGetAddToCartUrl()
    {
        $this->_testGetProductUrl('getAddToCartUrl', '/checkout/cart/add/');
    }

    public function testGetRemoveUrl()
    {
        $url = $this->_helper->getRemoveUrl();
        $this->assertStringContainsString('/catalog/product_compare/remove/', $url);
    }

    public function testGetClearListUrl()
    {
        $this->assertStringContainsString(
            '\/catalog\/product_compare\/clear\/',
            $this->_helper->getPostDataClearList()
        );
    }

    /**
     * @see testGetListUrl() for coverage of customer case
     */
    public function testGetItemCollection()
    {
        $this->assertInstanceOf(
            Collection::class,
            $this->_helper->getItemCollection()
        );
    }

    /**
     * calculate()
     * getItemCount()
     * hasItems()
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     * @magentoDbIsolation disabled
     */
    public function testCalculate()
    {
        /** @var Session $session */
        $session = $this->_objectManager->get(Session::class);
        try {
            $session->unsCatalogCompareItemsCount();
            $this->assertFalse($this->_helper->hasItems());
            $this->assertEquals(0, $session->getCatalogCompareItemsCount());

            $this->_populateCompareList();
            $this->_helper->calculate();
            $this->assertEquals(2, $session->getCatalogCompareItemsCount());
            $this->assertTrue($this->_helper->hasItems());

            $session->unsCatalogCompareItemsCount();
        } catch (Exception $e) {
            $session->unsCatalogCompareItemsCount();
            throw $e;
        }
    }

    /**
     * Add products from fixture to compare list
     */
    protected function _populateCompareList()
    {
        $productRepository = $this->_objectManager->create(ProductRepositoryInterface::class);
        $productOne = $productRepository->get('simple1');
        $productTwo = $productRepository->get('simple2');
        /** @var $compareList ListCompare */
        $compareList = $this->_objectManager->create(ListCompare::class);
        $compareList->addProduct($productOne)->addProduct($productTwo);
    }

    public function testSetGetAllowUsedFlat()
    {
        $this->assertTrue($this->_helper->getAllowUsedFlat());
        $this->_helper->setAllowUsedFlat(false);
        $this->assertFalse($this->_helper->getAllowUsedFlat());
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_helper = $this->_objectManager->get(Compare::class);
    }
}
