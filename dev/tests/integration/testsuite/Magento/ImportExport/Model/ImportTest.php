<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Model;

use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\ImportExport\Model\Import\AbstractSource;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import\ImageDirectoryBaseProvider;
use Magento\ImportExport\Model\Source\Import\Behavior\Basic;
use Magento\ImportExport\Model\Source\Import\Behavior\Custom;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/ImportExport/_files/import_data.php
 */
class ImportTest extends TestCase
{
    /**
     * Model object which is used for tests
     *
     * @var Import
     */
    protected $_model;

    /**
     * @var Import\Config
     */
    protected $_importConfig;

    /**
     * Expected entity behaviors
     *
     * @var array
     */
    protected $_entityBehaviors = [
        'catalog_product' => [
            'token' => Basic::class,
            'code' => 'basic_behavior',
            'notes' => [],
        ],
        'customer_composite' => [
            'token' => Basic::class,
            'code' => 'basic_behavior',
            'notes' => [],
        ],
        'customer' => [
            'token' => Custom::class,
            'code' => 'custom_behavior',
            'notes' => [],
        ],
        'customer_address' => [
            'token' => Custom::class,
            'code' => 'custom_behavior',
            'notes' => [],
        ],
    ];

    /**
     * Expected unique behaviors
     *
     * @var array
     */
    protected $_uniqueBehaviors = [
        'basic_behavior' => Basic::class,
        'custom_behavior' => Custom::class,
    ];

    /**
     * Test validation of images directory against provided base directory.
     *
     * @return void
     */
    public function testImagesDirBase(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Images file directory is outside required directory');

        $this->_model->setData(
            Import::FIELD_NAME_VALIDATION_STRATEGY,
            ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS
        );
        $this->_model->setData(Import::FIELD_NAME_IMG_FILE_DIR, '../_files');
        $this->_model->importSource();
    }

    /**
     * @covers \Magento\ImportExport\Model\Import::_getEntityAdapter
     */
    public function testImportSource()
    {
        /** @var $customersCollection Collection */
        $customersCollection = Bootstrap::getObjectManager()->create(
            Collection::class
        );

        $existCustomersCount = count($customersCollection->load());

        $customersCollection->resetData();
        $customersCollection->clear();

        $this->_model->setData(
            Import::FIELD_NAME_VALIDATION_STRATEGY,
            ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS
        );
        $this->_model->importSource();

        $customers = $customersCollection->getItems();

        $addedCustomers = count($customers) - $existCustomersCount;

        $this->assertGreaterThan($existCustomersCount, $addedCustomers);
    }

    public function testValidateSource()
    {
        $validationStrategy = ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_STOP_ON_ERROR;

        $this->_model->setEntity('catalog_product');
        $this->_model->setData(Import::FIELD_NAME_VALIDATION_STRATEGY, $validationStrategy);
        $this->_model->setData(Import::FIELD_NAME_ALLOWED_ERROR_COUNT, 0);

        /** @var AbstractSource|MockObject $source */
        $source = $this->getMockForAbstractClass(
            AbstractSource::class,
            [['sku', 'name']]
        );
        $source->expects($this->any())->method('_getNextRow')->willReturn(false);
        $this->assertTrue($this->_model->validateSource($source));
    }

    /**
     */
    public function testValidateSourceException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Entity is unknown');

        $source = $this->getMockForAbstractClass(
            AbstractSource::class,
            [],
            '',
            false
        );
        $this->_model->validateSource($source);
    }

    /**
     */
    public function testGetUnknownEntity()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Entity is unknown');

        $entityName = 'entity_name';
        $this->_model->setEntity($entityName);
        $this->assertSame($entityName, $this->_model->getEntity());
    }

    public function testGetEntity()
    {
        $entityName = 'catalog_product';
        $this->_model->setEntity($entityName);
        $this->assertSame($entityName, $this->_model->getEntity());
    }

    /**
     */
    public function testGetEntityEntityIsNotSet()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Entity is unknown');

        $this->_model->getEntity();
    }

    /**
     * Test getEntityBehaviors with all required data
     * Can't check array on equality because this test should be useful for CE
     *
     * @covers \Magento\ImportExport\Model\Import::getEntityBehaviors
     */
    public function testGetEntityBehaviors()
    {
        $this->prepareProductNotes();

        $importModel = $this->_model;
        $actualBehaviors = $importModel->getEntityBehaviors();

        foreach ($this->_entityBehaviors as $entityKey => $behaviorData) {
            $this->assertArrayHasKey($entityKey, $actualBehaviors);
            $this->assertEquals($behaviorData, $actualBehaviors[$entityKey]);
        }
    }

    /**
     * Add Catalog Products Notes to expected results.
     *
     * @return void
     * @ SuppressWarnings(PHPMD.)
     */
    private function prepareProductNotes(): void
    {
        $this->_entityBehaviors['catalog_product']['notes'] =
            [
                Import::BEHAVIOR_APPEND => new Phrase('New product data is added to the existing product data for'
                    . ' the existing entries in the database. All fields except sku can be updated.'),
                Import::BEHAVIOR_REPLACE => new Phrase('The existing product data is replaced with new data.'
                    . ' <b>Exercise caution when replacing data because the existing product data will be completely'
                    . ' cleared and all references in the system will be lost.</b>'),
                Import::BEHAVIOR_DELETE => new  Phrase('Any entities in the import data that already exist in the'
                    . ' database are deleted from the database.'),
            ];
    }

    /**
     * Test getEntityBehaviors with not existing behavior class
     *
     */
    public function testGetEntityBehaviorsWithUnknownBehavior()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The behavior token for customer is invalid.');

        $this->_importConfig->merge(
            ['entities' => ['customer' => ['behaviorModel' => 'Unknown_Behavior_Class']]]
        );
        $importModel = $this->_model;
        $actualBehaviors = $importModel->getEntityBehaviors();
        $this->assertArrayNotHasKey('customer', $actualBehaviors);
    }

    /**
     * Test getUniqueEntityBehaviors with all required data
     * Can't check array on equality because this test should be useful for CE
     *
     * @covers \Magento\ImportExport\Model\Import::getUniqueEntityBehaviors
     */
    public function testGetUniqueEntityBehaviors()
    {
        $importModel = $this->_model;
        $actualBehaviors = $importModel->getUniqueEntityBehaviors();

        foreach ($this->_uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $this->assertArrayHasKey($behaviorCode, $actualBehaviors);
            $this->assertEquals($behaviorClass, $actualBehaviors[$behaviorCode]);
        }
    }

    protected function setUp(): void
    {
        $this->_importConfig = Bootstrap::getObjectManager()->create(
            Import\Config::class
        );
        /** @var ImageDirectoryBaseProvider $provider */
        $provider = Bootstrap::getObjectManager()->get(ImageDirectoryBaseProvider::class);
        $this->_model = Bootstrap::getObjectManager()->create(
            Import::class,
            [
                'importConfig' => $this->_importConfig
            ]
        );
        $this->_model->setData('images_base_directory', $provider->getDirectory());
    }
}
