<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Listener of PHPUnit built-in events
 */

namespace Magento\TestFramework\Event;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\EventManager;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Throwable;

class PhpUnit implements TestListener
{
    /**
     * Used when PHPUnit framework instantiates the class on its own and passes nothing to the constructor
     *
     * @var EventManager
     */
    protected static $_defaultEventManager;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @param EventManager $eventManager
     * @throws LocalizedException
     */
    public function __construct(EventManager $eventManager = null)
    {
        $this->_eventManager = $eventManager ?: self::$_defaultEventManager;
        if (!$this->_eventManager) {
            throw new LocalizedException(__('Instance of the event manager is required.'));
        }
    }

    /**
     * Assign default event manager instance
     *
     * @param EventManager $eventManager
     */
    public static function setDefaultEventManager(EventManager $eventManager = null)
    {
        self::$_defaultEventManager = $eventManager;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(Test $test, Throwable $t, float $time): void
    {
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(
        Test                 $test,
        AssertionFailedError $e,
        float                                   $time
    ): void
    {
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(Test $test, Throwable $t, float $time): void
    {
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(Test $test, Throwable $t, float $time): void
    {
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(Test $test, Throwable $t, float $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function startTestSuite(TestSuite $suite): void
    {
        /* PHPUnit runs tests with data provider in own test suite for each test, so just skip such test suites */
        if ($suite instanceof DataProviderTestSuite) {
            return;
        }
        $this->_eventManager->fireEvent('startTestSuite');
    }

    /**
     * @inheritDoc
     */
    public function endTestSuite(TestSuite $suite): void
    {
        if ($suite instanceof DataProviderTestSuite) {
            return;
        }
        $this->_eventManager->fireEvent('endTestSuite', [$suite], true);
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test): void
    {
        if (!$test instanceof TestCase || $test instanceof Warning) {
            return;
        }
        $this->_eventManager->fireEvent('startTest', [$test]);
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(Test $test, float $time): void
    {
        if (!$test instanceof TestCase || $test instanceof Warning) {
            return;
        }
        $this->_eventManager->fireEvent('endTest', [$test], true);
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }
}
