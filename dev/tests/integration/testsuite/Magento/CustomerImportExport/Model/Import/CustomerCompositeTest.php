<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerImportExport\Model\Import;

use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Adapter;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for CustomerComposite import class
 */
class CustomerCompositeTest extends TestCase
{
    /**#@+
     * Attributes used in test assertions
     */
    const ATTRIBUTE_CODE_FIRST_NAME = 'firstname';

    const ATTRIBUTE_CODE_LAST_NAME = 'lastname';

    /**#@-*/

    /**#@+
     * Source *.csv file names for different behaviors
     */
    const UPDATE_FILE_NAME = 'customer_composite_update.csv';

    const DELETE_FILE_NAME = 'customer_composite_delete.csv';

    /**#@-*/

    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Composite customer entity adapter instance
     *
     * @var CustomerComposite
     */
    protected $_entityAdapter;

    /**
     * Additional customer attributes for assertion
     *
     * @var array
     */
    protected $_customerAttributes = [self::ATTRIBUTE_CODE_FIRST_NAME, self::ATTRIBUTE_CODE_LAST_NAME];

    /**
     * Customers and addresses before import, address ID is postcode
     *
     * @var array
     */
    protected $_beforeImport = [
        'betsyparker@example.com' => [
            'addresses' => ['19107', '72701'],
            'data' => [self::ATTRIBUTE_CODE_FIRST_NAME => 'Betsy', self::ATTRIBUTE_CODE_LAST_NAME => 'Parker'],
        ],
    ];

    /**
     * Customers and addresses after import, address ID is postcode
     *
     * @var array
     */
    protected $_afterImport = [
        'betsyparker@example.com' => [
            'addresses' => ['19107', '72701', '19108'],
            'data' => [
                self::ATTRIBUTE_CODE_FIRST_NAME => 'NotBetsy',
                self::ATTRIBUTE_CODE_LAST_NAME => 'NotParker',
            ],
        ],
        'anthonyanealy@magento.com' => ['addresses' => ['72701', '92664']],
        'loribbanks@magento.com' => ['addresses' => ['98801']],
        'kellynilson@magento.com' => ['addresses' => []],
    ];

    /**
     * @param string $behavior
     * @param string $sourceFile
     * @param array $dataBefore
     * @param array $dataAfter
     * @param int $updatedItemsCount
     * @param int $createdItemsCount
     * @param int $deletedItemsCount
     * @param array $errors
     *
     * @magentoDataFixture Magento/Customer/_files/import_export/customers_for_address_import.php
     * @magentoAppIsolation enabled
     *
     * @dataProvider importDataDataProvider
     */
    public function testImportData(
        $behavior,
        $sourceFile,
        array $dataBefore,
        array $dataAfter,
        $updatedItemsCount,
        $createdItemsCount,
        $deletedItemsCount,
        array $errors = []
    )
    {
        Bootstrap::getInstance()
            ->loadArea(Area::AREA_FRONTEND);
        // set entity adapter parameters
        $this->_entityAdapter->setParameters(['behavior' => $behavior]);
        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->create(Filesystem::class);
        $rootDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);

        $this->_entityAdapter->getErrorAggregator()->initValidationStrategy(
            ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_STOP_ON_ERROR,
            10
        );

        // set fixture CSV file
        $result = $this->_entityAdapter->setSource(
            Adapter::findAdapterFor($sourceFile, $rootDirectory)
        )
            ->validateData()
            ->hasToBeTerminated();
        if ($errors) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }

        // assert validation errors
        // can't use error codes because entity adapter gathers only error messages from aggregated adapters
        $actualErrors = array_values($this->_entityAdapter->getErrorAggregator()->getRowsGroupedByErrorCode());
        $this->assertEquals($errors, $actualErrors);

        // assert data before import
        $this->_assertCustomerData($dataBefore);

        // import data
        $this->_entityAdapter->importData();
        $this->assertSame($updatedItemsCount, $this->_entityAdapter->getUpdatedItemsCount());
        $this->assertSame($createdItemsCount, $this->_entityAdapter->getCreatedItemsCount());
        $this->assertSame($deletedItemsCount, $this->_entityAdapter->getDeletedItemsCount());

        // assert data after import
        $this->_assertCustomerData($dataAfter);
    }

    /**
     * Assertion of current customer and address data
     *
     * @param array $expectedData
     */
    protected function _assertCustomerData(array $expectedData)
    {
        /** @var $collection Collection */
        $collection = $this->_objectManager->create(Collection::class);
        $collection->addAttributeToSelect($this->_customerAttributes);
        $customers = $collection->getItems();

        $this->assertSameSize($expectedData, $customers);

        /** @var $customer \Magento\Customer\Model\Customer */
        foreach ($customers as $customer) {
            // assert customer existence
            $email = strtolower($customer->getEmail());
            $this->assertArrayHasKey($email, $expectedData);

            // assert customer data (only for required customers)
            if (isset($expectedData[$email]['data'])) {
                foreach ($expectedData[$email]['data'] as $attribute => $expectedValue) {
                    $this->assertEquals($expectedValue, $customer->getData($attribute));
                }
            }

            // assert address data
            $addresses = $customer->getAddresses();
            $this->assertSameSize($expectedData[$email]['addresses'], $addresses);
            /** @var $address \Magento\Customer\Model\Address */
            foreach ($addresses as $address) {
                $this->assertContains($address->getData('postcode'), $expectedData[$email]['addresses']);
            }
        }
    }

    /**
     * Data provider for testImportData
     *
     * @return array
     */
    public function importDataDataProvider()
    {
        $filesDirectory = __DIR__ . '/_files/';
        $sourceData = [
            'delete_behavior' => [
                '$behavior' => Import::BEHAVIOR_DELETE,
                '$sourceFile' => $filesDirectory . self::DELETE_FILE_NAME,
                '$dataBefore' => $this->_beforeImport,
                '$dataAfter' => [],
                '$updatedItemsCount' => 0,
                '$createdItemsCount' => 0,
                '$deletedItemsCount' => 1,
                '$errors' => [],
            ],
        ];

        $sourceData['add_update_behavior'] = [
            '$behavior' => Import::BEHAVIOR_ADD_UPDATE,
            '$sourceFile' => $filesDirectory . self::UPDATE_FILE_NAME,
            '$dataBefore' => $this->_beforeImport,
            '$dataAfter' => $this->_afterImport,
            '$updatedItemsCount' => 1,
            '$createdItemsCount' => 3,
            '$deletedItemsCount' => 0,
            '$errors' => [],
        ];

        return $sourceData;
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_entityAdapter = $this->_objectManager->create(
            CustomerComposite::class
        );
    }
}
