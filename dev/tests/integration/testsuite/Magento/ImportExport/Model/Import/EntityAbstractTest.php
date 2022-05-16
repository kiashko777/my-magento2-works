<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\AbstractEntity
 */

namespace Magento\ImportExport\Model\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import\Source\Csv;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityAbstractTest extends TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     */
    public function testSaveValidatedBunches()
    {
        $filesystem = Bootstrap::getObjectManager()
            ->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = new Csv(
            __DIR__ . '/Entity/_files/customers_for_validation_test.csv',
            $directory
        );
        $source->rewind();
        $expected = $source->current();

        $objectManager = Bootstrap::getObjectManager();
        /** @var $model AbstractEntity|MockObject */
        $model = $this->getMockForAbstractClass(
            AbstractEntity::class,
            [
                $objectManager->get(StringUtils::class),
                $objectManager->get(ScopeConfigInterface::class),
                $objectManager->get(ImportFactory::class),
                $objectManager->get(Helper::class),
                $objectManager->get(ResourceConnection::class),
                $objectManager->get(
                    ProcessingErrorAggregatorInterface::class
                )
            ],
            '',
            true,
            false,
            true,
            ['getMasterAttributeCode', 'validateRow', 'getEntityTypeCode']
        );
        $model->expects($this->any())->method('getMasterAttributeCode')->willReturn("email");
        $model->expects($this->any())->method('validateRow')->willReturn(true);
        $model->expects($this->any())->method('getEntityTypeCode')->willReturn('customer');

        $model->setSource($source);

        $method = new ReflectionMethod($model, '_saveValidatedBunches');
        $method->setAccessible(true);
        $method->invoke($model);

        /** @var $dataSourceModel Data */
        $dataSourceModel = $objectManager->get(Data::class);
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}
