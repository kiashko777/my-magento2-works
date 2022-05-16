<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\ProductTest;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\CatalogImportExport\Model\Import\ProductTestBase;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\ImportExport\Helper\Data;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\Source\Csv;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use function array_merge;
use function array_unique;

/**
 * Integration test for \Magento\CatalogImportExport\Model\Import\Products class.
 *
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoAppArea Adminhtml
 * @magentoDataFixtureBeforeTransaction Magento/Catalog/_files/enable_reindex_schedule.php
 * @magentoDataFixtureBeforeTransaction Magento/Catalog/_files/enable_catalog_product_reindex_schedule.php
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductMultipleStoresTest extends ProductTestBase
{
    /**
     * @magentoDataFixture Magento/Store/_files/website.php
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDbIsolation disabled
     */
    public function testProductWithMultipleStoresInDifferentBunches()
    {
        $products = [
            'simple1',
            'simple2',
            'simple3'
        ];

        $importExportData = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $importExportData->expects($this->atLeastOnce())
            ->method('getBunchSize')
            ->willReturn(1);
        $this->_model = $this->objectManager->create(
            \Magento\CatalogImportExport\Model\Import\Product::class,
            ['importExportData' => $importExportData]
        );

        $filesystem = $this->objectManager->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = $this->objectManager->create(
            Csv::class,
            [
                'file' => __DIR__ . '/../_files/products_to_import_with_multiple_store.csv',
                'directory' => $directory
            ]
        );
        $errors = $this->_model->setParameters(
            ['behavior' => Import::BEHAVIOR_APPEND, 'entity' => 'catalog_product']
        )->setSource(
            $source
        )->validateData();

        $this->assertTrue($errors->getErrorsCount() == 0);

        $this->_model->importData();
        $productCollection = $this->objectManager
            ->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $this->assertCount(3, $productCollection->getItems());
        $actualProductSkus = array_map(
            function (ProductInterface $item) {
                return $item->getSku();
            },
            $productCollection->getItems()
        );
        sort($products);
        $result = array_values($actualProductSkus);
        sort($result);
        $this->assertEquals(
            $products,
            $result
        );

        /** @var Registry $registry */
        $registry = $this->objectManager->get(Registry::class);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $productSkuList = ['simple1', 'simple2', 'simple3'];
        $categoryIds = [];
        foreach ($productSkuList as $sku) {
            try {
                /** @var ProductRepositoryInterface $productRepository */
                $productRepository = Bootstrap::getObjectManager()
                    ->get(ProductRepositoryInterface::class);
                /** @var Product $product */
                $product = $productRepository->get($sku, true);
                $categoryIds[] = $product->getCategoryIds();
                if ($product->getId()) {
                    $productRepository->delete($product);
                }
                // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
            } catch (NoSuchEntityException $e) {
                //Products already removed
            }
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = Bootstrap::getObjectManager()
            ->create(Collection::class);
        $collection
            ->addAttributeToFilter('entity_id', ['in' => array_unique(array_merge([], ...$categoryIds))])
            ->load()
            ->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * Test import product into multistore system when media is disabled.
     *
     * @magentoDataFixture Magento/CatalogImportExport/Model/Import/_files/custom_category_store_media_disabled.php
     * @return void
     */
    public function testProductsWithMultipleStoresWhenMediaIsDisabled(): void
    {
        $this->loginAdminUserWithUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);

        $filesystem = $this->objectManager->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = $this->objectManager->create(
            Csv::class,
            [
                'file' => __DIR__ . '/../_files/product_with_custom_store_media_disabled.csv',
                'directory' => $directory,
            ]
        );
        $errors = $this->_model->setParameters(
            [
                'behavior' => Import::BEHAVIOR_APPEND,
                'entity' => 'catalog_product',
            ]
        )->setSource(
            $source
        )->validateData();

        $errorMessages = array_map(
            function ($value) {
                /** @var ProcessingError $value */
                return $value->getErrorMessage();
            },
            $errors->getAllErrors()
        );
        $this->assertTrue($errors->getErrorsCount() === 0, "Error messages:\n" . implode("\n", $errorMessages));
        $this->assertTrue($this->_model->importData());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/Store/_files/website.php
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/_files/attribute_with_option.php
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_attribute.php
     * @magentoDbIsolation disabled
     */
    public function testProductsWithMultipleStores()
    {
        $objectManager = Bootstrap::getObjectManager();

        $filesystem = $objectManager->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = $this->objectManager->create(
            Csv::class,
            [
                'file' => __DIR__ . '/../_files/products_multiple_stores.csv',
                'directory' => $directory
            ]
        );
        $errors = $this->_model->setParameters(
            ['behavior' => Import::BEHAVIOR_APPEND, 'entity' => 'catalog_product']
        )->setSource(
            $source
        )->validateData();

        $this->assertTrue($errors->getErrorsCount() == 0);

        $this->_model->importData();

        /** @var Product $product */
        $product = $objectManager->create(Product::class);
        $id = $product->getIdBySku('Configurable 03');
        $product->load($id);
        $this->assertEquals('1', $product->getHasOptions());

        $objectManager->get(StoreManagerInterface::class)->setCurrentStore('fixturestore');

        /** @var Product $simpleProduct */
        $simpleProduct = $objectManager->create(Product::class);
        $id = $simpleProduct->getIdBySku('Configurable 03-Option 1');
        $simpleProduct->load($id);
        $this->assertTrue(count($simpleProduct->getWebsiteIds()) == 2);
        $this->assertEquals('Option Label', $simpleProduct->getAttributeText('attribute_with_option'));
    }

    /**
     * Test url keys properly generated in multistores environment.
     *
     * @magentoConfigFixture current_store catalog/seo/product_use_categories 1
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Catalog/_files/category_with_two_stores.php
     */
    public function testGenerateUrlsWithMultipleStores()
    {
        $objectManager = Bootstrap::getObjectManager();

        $filesystem = $objectManager->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = $this->objectManager->create(
            Csv::class,
            [
                'file' => __DIR__ . '/../_files/products_to_import_with_two_stores.csv',
                'directory' => $directory
            ]
        );
        $errors = $this->_model->setParameters(
            ['behavior' => Import::BEHAVIOR_ADD_UPDATE, 'entity' => 'catalog_product']
        )->setSource(
            $source
        )->validateData();

        $this->assertTrue($errors->getErrorsCount() == 0);

        $this->_model->importData();
        $this->assertProductRequestPath('default', 'category-defaultstore/product-default.html');
        $this->assertProductRequestPath('fixturestore', 'category-fixturestore/product-fixture.html');
    }
}
