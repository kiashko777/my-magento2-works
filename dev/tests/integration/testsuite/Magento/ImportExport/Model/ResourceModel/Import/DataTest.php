<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Model\ResourceModel\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test Import Data resource model
 *
 * @magentoDataFixture Magento/ImportExport/_files/import_data.php
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $_model;

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is unique
     */
    public function testGetUniqueColumnData()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get(
            Registry::class
        )->registry(
            '_fixture/Magento_ImportExport_Import_Data'
        );

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getUniqueColumnData('entity'));
    }

    /**
     * Test getUniqueColumnData() in case when in data stored in requested column is NOT unique
     *
     */
    public function testGetUniqueColumnDataException()
    {
        $this->expectException(LocalizedException::class);

        $this->_model->getUniqueColumnData('data');
    }

    /**
     * Test successful getBehavior()
     */
    public function testGetBehavior()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get(
            Registry::class
        )->registry(
            '_fixture/Magento_ImportExport_Import_Data'
        );

        $this->assertEquals($expectedBunches[0]['behavior'], $this->_model->getBehavior());
    }

    /**
     * Test successful getEntityTypeCode()
     */
    public function testGetEntityTypeCode()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        $expectedBunches = $objectManager->get(
            Registry::class
        )->registry(
            '_fixture/Magento_ImportExport_Import_Data'
        );

        $this->assertEquals($expectedBunches[0]['entity'], $this->_model->getEntityTypeCode());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->_model = Bootstrap::getObjectManager()->create(
            Data::class
        );
    }
}
