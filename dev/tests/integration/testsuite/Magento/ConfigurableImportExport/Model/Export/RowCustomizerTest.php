<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableImportExport\Model\Export;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class RowCustomizerTest extends TestCase
{
    /**
     * @var RowCustomizer
     */
    private $model;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testPrepareData()
    {
        $collection = $this->objectManager->get(Collection::class);
        $select = (string)$collection->getSelect();
        $this->model->prepareData($collection, [1, 2, 3, 4]);
        $this->assertEquals($select, (string)$collection->getSelect());
        $result = $this->model->addData([], 1);
        $this->assertArrayHasKey('configurable_variations', $result);
        $this->assertArrayHasKey('configurable_variation_labels', $result);
        $this->assertEquals(
            'sku=simple_10,test_configurable=Option 1|sku=simple_20,test_configurable=Option 2',
            $result['configurable_variations']
        );
        $this->assertEquals(
            'test_configurable=Test Configurable',
            $result['configurable_variation_labels']
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(
            RowCustomizer::class
        );
    }
}
