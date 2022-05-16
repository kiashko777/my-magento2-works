<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test DB "transparent transaction" features in DB adapter substitutes of integration tests
 *
 * Test behavior of all methods assumed by this interface
 * Due to current architecture of DB adapters, they are copy-pasted.
 * So we need to make sure all these classes have exactly the same behavior.
 */

namespace Magento\Test\Db\Adapter;

use Magento\TestFramework\Db\Adapter\TransactionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransactionInterfaceTest extends TestCase
{
    /**
     * @param string $class
     * @dataProvider transparentTransactionDataProvider
     */
    public function testBeginTransparentTransaction($class)
    {
        $connectionMock = $this->_getConnectionMock($class);
        $uniqid = uniqid();
        $connectionMock->expects($this->once())->method('beginTransaction')->willReturn($uniqid);
        $this->assertSame(0, $connectionMock->getTransactionLevel());
        $this->assertEquals($uniqid, $connectionMock->beginTransparentTransaction());
        $this->assertSame(0, $connectionMock->getTransactionLevel());
    }

    /**
     * Instantiate specified adapter class and block all methods that would try to execute real queries
     *
     * @param string $class
     * @return TransactionInterface|MockObject
     */
    protected function _getConnectionMock($class)
    {
        $connection = $this->createPartialMock($class, ['beginTransaction', 'rollback', 'commit']);
        $this->assertInstanceOf(TransactionInterface::class, $connection);
        return $connection;
    }

    /**
     * @param string $class
     * @dataProvider transparentTransactionDataProvider
     */
    public function testRollbackTransparentTransaction($class)
    {
        $connectionMock = $this->_getConnectionMock($class);
        $uniqid = uniqid();
        $connectionMock->expects($this->once())->method('rollback')->willReturn($uniqid);
        $connectionMock->beginTransparentTransaction();
        $this->assertEquals($uniqid, $connectionMock->rollbackTransparentTransaction());
        $this->assertSame(0, $connectionMock->getTransactionLevel());
    }

    /**
     * @param string $class
     * @dataProvider transparentTransactionDataProvider
     */
    public function testCommitTransparentTransaction($class)
    {
        $connectionMock = $this->_getConnectionMock($class);
        $uniqid = uniqid();
        $connectionMock->expects($this->once())->method('commit')->willReturn($uniqid);
        $connectionMock->beginTransparentTransaction();
        $this->assertEquals($uniqid, $connectionMock->commitTransparentTransaction());
        $this->assertSame(0, $connectionMock->getTransactionLevel());
    }

    /**
     * @return array
     */
    public function transparentTransactionDataProvider()
    {
        $result = [];
        $path = '/../../../../../../../Magento/TestFramework/Db/Adapter';
        foreach (glob(realpath(__DIR__ . $path) . '/*.php') as $file) {
            $suffix = basename($file, '.php');
            if (false === strpos($suffix, 'Interface')) {
                $result[] = ["Magento\\TestFramework\\Db\\Adapter\\{$suffix}"];
            }
        }
        return $result;
    }
}
