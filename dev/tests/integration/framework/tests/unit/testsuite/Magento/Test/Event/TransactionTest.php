<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Event\Transaction.
 */

namespace Magento\Test\Event;

use Magento\TestFramework\Db\Adapter\Mysql;
use Magento\TestFramework\Db\Adapter\TransactionInterface;
use Magento\TestFramework\Event\Transaction;
use Magento\TestFramework\EventManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @var Transaction|MockObject
     */
    protected $_object;

    /**
     * @var EventManager|MockObject
     */
    protected $_eventManager;

    /**
     * @var TransactionInterface|MockObject
     */
    protected $_adapter;

    /**
     * @param string $method
     * @param string $eventName
     * @dataProvider startAndRollbackTransactionDataProvider
     */
    public function testStartAndRollbackTransaction($method, $eventName)
    {
        $this->_imitateTransactionStartRequest($eventName);
        $this->_expectTransactionStart($this->at(1));
        $this->_object->{$method}($this);

        $this->_imitateTransactionRollbackRequest($eventName);
        $this->_expectTransactionRollback($this->at(1));
        $this->_object->{$method}($this);
    }

    /**
     * Imitate transaction start request
     *
     * @param string $eventName
     */
    protected function _imitateTransactionStartRequest($eventName)
    {
        $callback = function ($eventName, array $parameters) {
            /** @var $param \Magento\TestFramework\Event\Param\Transaction */
            $param = $parameters[1];
            $param->requestTransactionStart();
        };
        $this->_eventManager->expects(
            $this->at(0)
        )->method(
            'fireEvent'
        )->with(
            $eventName
        )->willReturnCallback(
            $callback
        );
    }

    /**
     * Setup expectations for "transaction start" use case
     *
     * @param InvocationOrder $invocationMatcher
     */
    protected function _expectTransactionStart(InvocationOrder $invocationMatcher)
    {
        $this->_eventManager->expects($invocationMatcher)->method('fireEvent')->with('startTransaction');
        $this->_adapter->expects($this->once())->method('beginTransaction');
    }

    /**
     * Imitate transaction rollback request
     *
     * @param string $eventName
     */
    protected function _imitateTransactionRollbackRequest($eventName)
    {
        $callback = function ($eventName, array $parameters) {
            /** @var $param \Magento\TestFramework\Event\Param\Transaction */
            $param = $parameters[1];
            $param->requestTransactionRollback();
        };
        $this->_eventManager->expects(
            $this->at(0)
        )->method(
            'fireEvent'
        )->with(
            $eventName
        )->willReturnCallback(
            $callback
        );
    }

    /**
     * Setup expectations for "transaction rollback" use case
     *
     * @param InvocationOrder $invocationMatcher
     */
    protected function _expectTransactionRollback(InvocationOrder $invocationMatcher)
    {
        $this->_eventManager->expects($invocationMatcher)->method('fireEvent')->with('rollbackTransaction');
        $this->_adapter->expects($this->once())->method('rollback');
    }

    public function startAndRollbackTransactionDataProvider()
    {
        return [
            'method "startTest"' => ['startTest', 'startTestTransactionRequest'],
            'method "endTest"' => ['endTest', 'endTestTransactionRequest']
        ];
    }

    /**
     * @param string $method
     * @param string $eventName
     * @dataProvider startAndRollbackTransactionDataProvider
     */
    public function testDoNotStartAndRollbackTransaction($method, $eventName)
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with($eventName);
        $this->_adapter->expects($this->never())->method($this->anything());
        $this->_object->{$method}($this);
    }

    public function testEndTestSuiteDoNothing()
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        $this->_adapter->expects($this->never())->method($this->anything());
        $this->_object->endTestSuite();
    }

    public function testEndTestSuiteRollbackTransaction()
    {
        $this->_imitateTransactionStartRequest('startTestTransactionRequest');
        $this->_object->startTest($this);

        $this->_expectTransactionRollback($this->once());
        $this->_object->endTestSuite();
    }

    protected function setUp(): void
    {
        $this->_eventManager = $this->getMockBuilder(EventManager::class)
            ->setMethods(['fireEvent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_adapter =
            $this->createPartialMock(Mysql::class, ['beginTransaction', 'rollBack']);
        $this->_object = $this->getMockBuilder(Transaction::class)
            ->setMethods(['_getConnection'])
            ->setConstructorArgs([$this->_eventManager])
            ->getMock();

        $this->_object->expects($this->any())->method('_getConnection')->willReturn($this->_adapter);
    }
}
