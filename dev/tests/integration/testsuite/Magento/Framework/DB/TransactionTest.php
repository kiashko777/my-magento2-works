<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DB;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Flag;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    protected $objectManager;

    /**
     * @var Transaction
     */
    protected $_model;

    /**
     * @magentoAppArea Adminhtml
     */
    public function testSaveDelete()
    {
        /** @var Flag $first */
        $first = $this->objectManager->create(Flag::class, ['data' => ['flag_code' => 'test1']]);
        $first->setFlagData('test1data');
        $second = $this->objectManager->create(Flag::class, ['data' => ['flag_code' => 'test2']]);
        $second->setFlagData('test2data');

        $first->save();
        $this->_model->addObject($first)->addObject($second, 'second');
        $this->_model->save();
        $this->assertNotEmpty($first->getId());
        $this->assertNotEmpty($second->getId());

        $this->_model->delete();

        $test = $this->objectManager->create(Flag::class);
        $test->load($first->getId());
        $this->assertEmpty($test->getId());
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testTransactionLevelDbIsolationDisable()
    {
        $resourceConnection = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $this->assertEquals(0, $resourceConnection->getConnection('default')->getTransactionLevel());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testTransactionLevelDbIsolationEnabled()
    {
        $resourceConnection = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $this->assertEquals(1, $resourceConnection->getConnection('default')->getTransactionLevel());
    }

    /**
     * @magentoDataFixture Magento/Framework/DB/_files/dummy_fixture.php
     */
    public function testTransactionLevelDbIsolationDefault()
    {
        $resourceConnection = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        $this->assertEquals(1, $resourceConnection->getConnection('default')->getTransactionLevel());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->_model = $this->objectManager
            ->create(Transaction::class);
    }
}
