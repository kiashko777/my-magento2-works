<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Attribute;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProductAttributesCleanUpTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var ProductAttributesCleanUp
     */
    private $command;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Attribute
     */
    private $attributeResource;

    /**
     * @magentoDataFixture Magento/Store/_files/website.php
     * @magentoDataFixture Magento/Store/_files/fixture_store_with_catalogsearch_index.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     */
    public function testExecute()
    {
        // Verify that unused attribute was created
        $attribute = $this->getUnusedProductAttribute();

        $this->assertArrayHasKey('value', $attribute);
        $this->assertArrayHasKey('value_id', $attribute);
        $this->assertEquals($attribute['value'], 'Simple fixture store');

        // Execute command
        $this->tester->execute([]);

        // Verify that unused attribute was removed
        $this->assertStringContainsString(
            'Unused product attributes successfully cleaned up',
            $this->tester->getDisplay()
        );
        $attribute = $this->getUnusedProductAttribute();
        $this->assertEmpty($attribute);
    }

    /**
     * @return array|false
     */
    private function getUnusedProductAttribute()
    {
        $connection = $this->attributeResource->getConnection();
        $select = $connection->select();
        $select->from($this->attributeResource->getTable('catalog_product_entity_varchar'));
        $select->where('value = ?', 'Simple fixture store');

        return $connection->fetchRow($select);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->command = $this->objectManager->create(ProductAttributesCleanUp::class);
        $this->attributeResource = $this->objectManager->create(Attribute::class);
        $this->tester = new CommandTester($this->command);

        // Prepare data fixtures for test
        $store = $this->prepareAdditionalStore();
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get('simple');
        $product->setName('Simple fixture store');
        $product->setStoreId($store->getId());
        $product->save();
    }

    /**
     * @return Store
     */
    private function prepareAdditionalStore()
    {
        /** @var Website $website */
        $website = $this->objectManager->create(Website::class);
        $website->load('test');

        /** @var Store $store */
        $store = $this->objectManager->create(Store::class);
        $store->load('fixturestore');

        /** @var Group $storeGroup */
        $storeGroup = $this->objectManager->create(Group::class);
        $storeGroup->setWebsiteId($website->getId());
        $storeGroup->setName('Fixture Store Group');
        $storeGroup->setRootCategoryId(2);
        $storeGroup->setDefaultStoreId($store->getId());
        $storeGroup->save();

        $store->setWebsiteId($website->getId())
            ->setGroupId($storeGroup->getId())
            ->save();

        return $store;
    }
}
