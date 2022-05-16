<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\ResourceModel\Category;

use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/Model/ResourceModel/_files/category_multiple_stores.php
     */
    public function testJoinUrlRewriteOnDefault()
    {
        $categories = $this->collection->joinUrlRewrite()->addPathFilter('1/2/3');
        $this->assertCount(1, $categories);
        /** @var $category Category */
        $category = $categories->getFirstItem();
        $this->assertStringEndsWith('category.html', $category->getUrl());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/Model/ResourceModel/_files/category_multiple_stores.php
     */
    public function testJoinUrlRewriteNotOnDefaultStore()
    {
        $store = Bootstrap::getObjectManager()
            ->create(Store::class);
        $storeId = $store->load('second_category_store', 'code')->getId();
        $categories = $this->collection->setStoreId($storeId)->joinUrlRewrite()->addPathFilter('1/2/3');
        $this->assertCount(1, $categories);
        /** @var $category Category */
        $category = $categories->getFirstItem();
        $this->assertStringEndsWith('category-3-on-2.html', $category->getUrl());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }

    protected function setDown()
    {
        /* Refresh stores memory cache after store deletion */
        Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->reinitStores();
    }
}
