<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ErrorLog;

use Exception;
use Magento\TestFramework\Helper;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Throwable;

class Listener implements TestListener
{
    /**
     * @var Logger
     */
    private $logger;

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
    }

    /**
     * @inheritDoc
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test): void
    {
        $this->logger = Helper\Bootstrap::getObjectManager()->get(Logger::class);
        $this->logger->clearMessages();
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(Test $test, float $time): void
    {
        if ($test instanceof TestCase) {
            $messages = $this->logger->getMessages();
            try {
                if ($messages) {
                    $test->assertEquals(
                        '',
                        var_export($messages, true),
                        'Errors were added to log during test execution.'
                    );
                }
            } catch (Exception $e) {
                $test->getTestResultObject()->addError($test, $e, 0);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }
}
