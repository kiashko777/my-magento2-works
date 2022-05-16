<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ImportExport\Model\Import\Entity;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import\Source\Csv;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\ImportExport\Model\Import\AbstractEntity
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityAbstractTest extends TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     *
     * @return void
     */
    public function testSaveValidatedBunches(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $filesystem = $objectManager->create(Filesystem::class);
        $directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $source = new Csv(__DIR__ . '/_files/advanced_price_for_validation_test.csv', $directory);
        $source->rewind();

        $eavConfig = $this->createMock(Config::class);
        $entityTypeMock = $this->createMock(Type::class);
        $eavConfig->expects($this->any())->method('getEntityType')->willReturn($entityTypeMock);

        /** @var $model AbstractEntity|MockObject */
        $model = $this->getMockForAbstractClass(
            AbstractEntity::class,
            [
                $objectManager->get(\Magento\Framework\Json\Helper\Data::class),
                $objectManager->get(\Magento\ImportExport\Helper\Data::class),
                $objectManager->get(Data::class),
                $eavConfig,
                $objectManager->get(ResourceConnection::class),
                $objectManager->get(Helper::class),
                $objectManager->get(StringUtils::class),
                $objectManager->get(ProcessingErrorAggregatorInterface::class),
            ],
            '',
            true,
            false,
            true,
            ['validateRow', 'getEntityTypeCode']
        );
        $model->expects($this->any())->method('validateRow')->willReturn(true);
        $model->expects($this->any())->method('getEntityTypeCode')->willReturn('catalog_product');

        $model->setSource($source);

        $method = new ReflectionMethod($model, '_saveValidatedBunches');
        $method->setAccessible(true);
        $method->invoke($model);

        $this->assertEquals(1, $model->getProcessedEntitiesCount());
    }
}
