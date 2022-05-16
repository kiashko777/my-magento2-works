<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Event;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Db\Adapter\TransactionInterface;
use Magento\TestFramework\EventManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Warning;

/**
 * Database transaction events manager
 */
class Transaction
{
    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @var Param\Transaction
     */
    protected $_eventParam;

    /**
     * @var bool
     */
    protected $_isTransactionActive = false;

    /**
     * Constructor
     *
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * Handler for 'startTest' event
     *
     * @param TestCase $test
     */
    public function startTest(TestCase $test)
    {
        $this->_processTransactionRequests('startTest', $test);
    }

    /**
     * Query whether there are any requests for transaction operations and performs them
     *
     * @param string $eventName
     * @param TestCase $test
     */
    protected function _processTransactionRequests($eventName, TestCase $test)
    {
        $param = $this->_getEventParam();
        $this->_eventManager->fireEvent($eventName . 'TransactionRequest', [$test, $param]);
        if ($param->isTransactionRollbackRequested()) {
            $this->_rollbackTransaction();
        }
        if ($param->isTransactionStartRequested()) {
            $this->_startTransaction($test);
        }
    }

    /**
     * Retrieve clean instance of transaction event parameter
     *
     * @return Param\Transaction
     */
    protected function _getEventParam()
    {
        /* reset object state instead of instantiating new object over and over again */
        if (!$this->_eventParam) {
            $this->_eventParam = new Param\Transaction();
        } else {
            $this->_eventParam->__construct();
        }
        return $this->_eventParam;
    }

    /**
     * Rollback transaction and fire 'rollbackTransaction' event
     */
    protected function _rollbackTransaction()
    {
        if ($this->_isTransactionActive) {
            $this->_getConnection()->rollbackTransparentTransaction();
            $this->_isTransactionActive = false;
            $this->_eventManager->fireEvent('rollbackTransaction');
            $this->_getConnection()->closeConnection();
        }
    }

    /**
     * Retrieve database adapter instance
     *
     * @param string $connectionName
     * @return AdapterInterface|TransactionInterface
     * @throws LocalizedException
     */
    protected function _getConnection($connectionName = ResourceConnection::DEFAULT_CONNECTION)
    {
        /** @var $resource ResourceConnection */
        $resource = Bootstrap::getObjectManager()
            ->get(ResourceConnection::class);
        return $resource->getConnection($connectionName);
    }

    /**
     * Start transaction and fire 'startTransaction' event
     *
     * @param TestCase $test
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _startTransaction(TestCase $test)
    {
        if (!$this->_isTransactionActive) {
            $this->_getConnection()->beginTransparentTransaction();
            $this->_isTransactionActive = true;
            try {
                /**
                 * Add any warning during transaction execution as a failure.
                 */
                set_error_handler(
                    function ($errNo, $errStr, $errFile, $errLine) use ($test) {
                        $errMsg = sprintf("%s: %s in %s:%s.", "Warning", $errStr, $errFile, $errLine);
                        $test->getTestResultObject()->addError($test, new Warning($errMsg), 0);

                        // Allow error to be handled by next error handler
                        return false;
                    },
                    E_WARNING
                );
                $this->_eventManager->fireEvent('startTransaction', [$test]);
                restore_error_handler();
            } catch (Exception $e) {
                $test->getTestResultObject()->addFailure(
                    $test,
                    new AssertionFailedError((string)$e),
                    0
                );
            }
        }
    }

    /**
     * Handler for 'endTest' event
     *
     * @param TestCase $test
     */
    public function endTest(TestCase $test)
    {
        $this->_processTransactionRequests('endTest', $test);
    }

    /**
     * Handler for 'endTestSuite' event
     */
    public function endTestSuite()
    {
        $this->_rollbackTransaction();
    }
}
