<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Annotation;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Annotation\DbIsolation;
use Magento\TestFramework\Event\Param\Transaction;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\TestFramework\Annotation\DbIsolation.
 *
 * @magentoDbIsolation enabled
 */
class DbIsolationTest extends TestCase
{
    /**
     * @var DbIsolation
     */
    protected $_object;

    /**
     * @magentoDbIsolation enabled
     */
    public function testStartTestTransactionRequestMethodIsolationEnabled()
    {
        $this->testStartTestTransactionRequestClassIsolationEnabled();
    }

    public function testStartTestTransactionRequestClassIsolationEnabled()
    {
        $eventParam = new Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertTrue($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());
    }

    /**
     * @magentoDbIsolation disabled
     */
    public function testStartTestTransactionRequestMethodIsolationDisabled()
    {
        $eventParam = new Transaction();
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Transaction();
        $this->_object->startTransaction($this);
        $this->_object->startTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertTrue($eventParam->isTransactionRollbackRequested());
    }

    /**
     * @magentoDbIsolation invalid
     */
    public function testStartTestTransactionRequestInvalidAnnotation()
    {
        $this->expectException(LocalizedException::class);

        $this->_object->startTestTransactionRequest($this, new Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testStartTestTransactionRequestAmbiguousAnnotation()
    {
        $this->expectException(LocalizedException::class);

        $this->_object->startTestTransactionRequest($this, new Transaction());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testEndTestTransactionRequestMethodIsolationEnabled()
    {
        $eventParam = new Transaction();
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertFalse($eventParam->isTransactionRollbackRequested());

        $eventParam = new Transaction();
        $this->_object->startTransaction($this);
        $this->_object->endTestTransactionRequest($this, $eventParam);
        $this->assertFalse($eventParam->isTransactionStartRequested());
        $this->assertTrue($eventParam->isTransactionRollbackRequested());
    }

    protected function setUp(): void
    {
        $this->_object = new DbIsolation();
    }
}
