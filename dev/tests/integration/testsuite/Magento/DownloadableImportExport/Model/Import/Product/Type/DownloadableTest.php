<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\DownloadableImportExport\Model\Import\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Downloadable\Api\DomainManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\EntityManager\EntityMetadata;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Source\Csv;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DownloadableTest extends TestCase
{
    /**
     * Downloadable product test Name
     */
    const TEST_PRODUCT_NAME = 'Downloadable 1';

    /**
     * Downloadable product test Type
     */
    const TEST_PRODUCT_TYPE = 'downloadable';

    /**
     * Downloadable product Links Group Name
     */
    const TEST_PRODUCT_LINKS_GROUP_NAME = 'TEST Import Links';

    /**
     * Downloadable product Samples Group Name
     */
    const TEST_PRODUCT_SAMPLES_GROUP_NAME = 'TEST Import Samples';
    /**
     * @var Product
     */
    protected $model;
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var EntityMetadata
     */
    protected $productMetadata;
    /**
     * @var DomainManagerInterface
     */
    private $domainManager;

    /**
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testDownloadableImport()
    {
        // import data from CSV file
        $pathToFile = __DIR__ . '/../../_files/import_downloadable.csv';
        $filesystem = $this->objectManager->create(
            Filesystem::class
        );

        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = $this->objectManager->create(
            Csv::class,
            [
                'file' => $pathToFile,
                'directory' => $directory
            ]
        );
        $errors = $this->model->setSource(
            $source
        )->setParameters(
            [
                'behavior' => Import::BEHAVIOR_APPEND,
                'entity' => 'catalog_product'
            ]
        )->validateData();

        $this->assertTrue($errors->getErrorsCount() == 0);
        $this->model->importData();

        $resource = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class);
        $productId = $resource->getIdBySku(self::TEST_PRODUCT_NAME);
        $this->assertIsNumeric($productId);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create(
            \Magento\Catalog\Model\Product::class
        );
        $product->load($productId);

        $this->assertFalse($product->isObjectNew());
        $this->assertEquals(self::TEST_PRODUCT_NAME, $product->getName());
        $this->assertEquals(self::TEST_PRODUCT_TYPE, $product->getTypeId());

        $downloadableProductLinks = $product->getExtensionAttributes()->getDownloadableProductLinks();
        $downloadableLinks = $product->getDownloadableLinks();
        $downloadableProductSamples = $product->getExtensionAttributes()->getDownloadableProductSamples();
        $downloadableSamples = $product->getDownloadableSamples();

        //TODO: Track Fields: id, link_id, link_file and sample_file)
        $expectedLinks = [
            'file' => [
                'title' => 'TEST Import link Title File',
                'sort_order' => '78',
                'sample_type' => 'file',
                'price' => 123,
                'number_of_downloads' => '123',
                'is_shareable' => '0',
                'link_type' => 'file'
            ],
            'url' => [
                'title' => 'TEST Import link Title URL',
                'sort_order' => '42',
                'sample_type' => 'url',
                'sample_url' => 'http://www.bing.com',
                'price' => 1,
                'number_of_downloads' => '0',
                'is_shareable' => '1',
                'link_type' => 'url',
                'link_url' => 'http://www.google.com'
            ]
        ];
        foreach ($downloadableProductLinks as $link) {
            $actualLink = $link->getData();
            $this->assertArrayHasKey('link_type', $actualLink);
            foreach ($expectedLinks[$actualLink['link_type']] as $expectedKey => $expectedValue) {
                $this->assertArrayHasKey($expectedKey, $actualLink);
                $this->assertEquals($actualLink[$expectedKey], $expectedValue);
            }
        }
        foreach ($downloadableLinks as $link) {
            $actualLink = $link->getData();
            $this->assertArrayHasKey('link_type', $actualLink);
            $this->assertArrayHasKey('product_id', $actualLink);
            $this->assertEquals($actualLink['product_id'], $product->getData($this->productMetadata->getLinkField()));
            foreach ($expectedLinks[$actualLink['link_type']] as $expectedKey => $expectedValue) {
                $this->assertArrayHasKey($expectedKey, $actualLink);
                $this->assertEquals($actualLink[$expectedKey], $expectedValue);
            }
        }

        //TODO: Track Fields: id, sample_id and sample_file)
        $expectedSamples = [
            'file' => [
                'title' => 'TEST Import Sample File',
                'sort_order' => '178',
                'sample_type' => 'file'
            ],
            'url' => [
                'title' => 'TEST Import Sample URL',
                'sort_order' => '178',
                'sample_type' => 'url',
                'sample_url' => 'http://www.yahoo.com'
            ]
        ];
        foreach ($downloadableProductSamples as $sample) {
            $actualSample = $sample->getData();
            $this->assertArrayHasKey('sample_type', $actualSample);
            foreach ($expectedSamples[$actualSample['sample_type']] as $expectedKey => $expectedValue) {
                $this->assertArrayHasKey($expectedKey, $actualSample);
                $this->assertEquals($actualSample[$expectedKey], $expectedValue);
            }
        }
        foreach ($downloadableSamples as $sample) {
            $actualSample = $sample->getData();
            $this->assertArrayHasKey('sample_type', $actualSample);
            $this->assertArrayHasKey('product_id', $actualSample);
            $this->assertEquals($actualSample['product_id'], $product->getData($this->productMetadata->getLinkField()));
            foreach ($expectedSamples[$actualSample['sample_type']] as $expectedKey => $expectedValue) {
                $this->assertArrayHasKey($expectedKey, $actualSample);
                $this->assertEquals($actualSample[$expectedKey], $expectedValue);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            Product::class
        );
        /** @var MetadataPool $metadataPool */
        $metadataPool = $this->objectManager->get(MetadataPool::class);
        $this->productMetadata = $metadataPool->getMetadata(ProductInterface::class);

        $this->domainManager = $this->objectManager->get(DomainManagerInterface::class);
        $this->domainManager->addDomains(['www.bing.com', 'www.google.com', 'www.yahoo.com']);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->domainManager->removeDomains(['www.bing.com', 'www.google.com', 'www.yahoo.com']);
    }
}
