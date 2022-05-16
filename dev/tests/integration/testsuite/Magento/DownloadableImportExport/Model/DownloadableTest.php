<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\DownloadableImportExport\Model;

use Magento\Catalog\Model\Product;
use Magento\CatalogImportExport\Model\AbstractProductExportImportTestCase;

/**
 * Test export and import downloadable products
 */
class DownloadableTest extends AbstractProductExportImportTestCase
{
    /**
     * @return array
     */
    public function exportImportDataProvider(): array
    {
        return [
            'downloadable-product' => [
                [
                    'Magento/Downloadable/_files/product_downloadable_with_link_url_and_sample_url.php'
                ],
                [
                    'downloadable-product',
                ],
            ],
        ];
    }

    /**
     * Run import/export tests.
     *
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     *
     * @param array $fixtures
     * @param string[] $skus
     * @param string[] $skippedAttributes
     * @return void
     * @dataProvider exportImportDataProvider
     */
    public function testImportExport(array $fixtures, array $skus, array $skippedAttributes = []): void
    {
        $skippedAttributes = array_merge(self::$skippedAttributes, ['downloadable_links']);
        parent::testImportExport($fixtures, $skus, $skippedAttributes);
    }

    /**
     * @inheritdoc
     */
    protected function assertEqualsSpecificAttributes(
        Product $expectedProduct,
        Product $actualProduct
    ): void
    {
        $expectedProductLinks = $expectedProduct->getExtensionAttributes()->getDownloadableProductLinks();
        $expectedProductSamples = $expectedProduct->getExtensionAttributes()->getDownloadableProductSamples();

        $actualProductLinks = $actualProduct->getExtensionAttributes()->getDownloadableProductLinks();
        $actualProductSamples = $actualProduct->getExtensionAttributes()->getDownloadableProductSamples();

        $this->assertEquals(count($expectedProductLinks), count($actualProductLinks));
        $this->assertEquals(count($expectedProductSamples), count($actualProductSamples));
        $actualLinks = $this->getDataWithSortingById($actualProductLinks);
        $expectedLinks = $this->getDataWithSortingById($actualProductLinks);
        foreach ($actualLinks as $key => $actualLink) {
            $this->assertEquals($expectedLinks[$key], $actualLink);
        }
        $actualSamples = $this->getDataWithSortingById($actualProductSamples);
        $expectedSamples = $this->getDataWithSortingById($expectedProductSamples);
        foreach ($actualSamples as $key => $actualSample) {
            $this->assertEquals($expectedSamples[$key], $actualSample);
        }
    }

    /**
     * Get data with sorting by id
     *
     * @param array $objects
     *
     * @return array
     */
    private function getDataWithSortingById(array $objects)
    {
        $result = [];
        foreach ($objects as $object) {
            $result[$object->getId()] = $object->getData();
        }

        return $result;
    }
}
