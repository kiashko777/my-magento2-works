<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

use Exception;
use Magento\Catalog\Helper\Product\Flat\Indexer;
use Magento\Catalog\Model\Indexer\Product\Flat\Action\Full as FlatIndexerFull;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;
use Zend_Db_Statement_Exception;

/**
 * Test relation customization
 *
 * @magentoDbIsolation disabled
 */
class RelationTest extends TestCase
{
    /**
     * @var FlatIndexerFull
     */
    private $indexer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * Updated flat tables
     *
     * @var array
     */
    private $flatUpdated = [];

    /**
     * @var Indexer
     */
    private $productIndexerHelper;

    /**
     * Test that SQL generated for relation customization is valid
     *
     * @return void
     * @throws LocalizedException
     * @throws Exception
     */
    public function testExecute(): void
    {
        $this->addChildColumns();
        try {
            $result = $this->indexer->execute();
        } catch (LocalizedException $e) {
            if ($e->getPrevious() instanceof Zend_Db_Statement_Exception) {
                $this->fail($e->getMessage());
            }
            throw $e;
        }
        $this->assertInstanceOf(FlatIndexerFull::class, $result);
    }

    /**
     * Add child columns to tables if needed
     *
     * @return void
     */
    private function addChildColumns(): void
    {
        foreach ($this->storeManager->getStores() as $store) {
            $flatTable = $this->productIndexerHelper->getFlatTableName($store->getId());
            if ($this->connection->isTableExists($flatTable)
                && !$this->connection->tableColumnExists($flatTable, 'child_id')
                && !$this->connection->tableColumnExists($flatTable, 'is_child')
            ) {
                $this->connection->addColumn(
                    $flatTable,
                    'child_id',
                    [
                        'type' => 'integer',
                        'length' => null,
                        'unsigned' => true,
                        'nullable' => true,
                        'default' => null,
                        'unique' => true,
                        'comment' => 'Child Id',
                    ]
                );
                $this->connection->addColumn(
                    $flatTable,
                    'is_child',
                    [
                        'type' => 'smallint',
                        'length' => 1,
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Checks If Entity Is Child',
                    ]
                );

                $this->flatUpdated[] = $flatTable;
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->productIndexerHelper = $objectManager->create(
            Indexer::class,
            ['addChildData' => true]
        );
        $this->indexer = $objectManager->create(
            FlatIndexerFull::class,
            [
                'productHelper' => $this->productIndexerHelper,
            ]
        );
        $this->storeManager = $objectManager->get(StoreManagerInterface::class);
        $this->connection = $objectManager->get(ResourceConnection::class)->getConnection();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        foreach ($this->flatUpdated as $flatTable) {
            $this->connection->dropColumn($flatTable, 'child_id');
            $this->connection->dropColumn($flatTable, 'is_child');
        }
    }
}
