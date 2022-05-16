<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoriesTest extends TestCase
{
    /**
     * @var Categories
     */
    private $object;

    public function testModifyMeta()
    {
        $inputMeta = include __DIR__ . '/_files/input_meta_for_categories.php';
        $expectedCategories = include __DIR__ . '/_files/expected_categories.php';
        $this->assertCategoriesInMeta($expectedCategories, $this->object->modifyMeta($inputMeta));
        // Verify cached data
        $this->assertCategoriesInMeta($expectedCategories, $this->object->modifyMeta($inputMeta));
    }

    private function assertCategoriesInMeta(array $expectedCategories, array $meta)
    {
        $categoriesElement = $meta['product-details']['children']['container_category_ids']['children']['category_ids'];
        $this->assertEquals($expectedCategories, $categoriesElement['arguments']['data']['config']['options']);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $registry = $objectManager->get(Registry::class);
        /** @var $store Store */
        $store = $objectManager->create(Store::class);
        $store->load('admin');
        $registry->register('current_store', $store);
        $product = $objectManager->create(Product::class);
        $registry->register('current_product', $product);
        $this->object = $objectManager->create(Categories::class);
    }
}
