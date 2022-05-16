<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\UseCase;

use Magento\Amqp\Model\Config;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\MessageQueue\EnvironmentPreconditionException;
use Magento\TestFramework\MessageQueue\PreconditionFailedException;
use Magento\TestFramework\MessageQueue\PublisherConsumerController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Base test case for message queue tests.
 */
class QueueTestCaseAbstract extends TestCase
{
    /**
     * @var string[]
     */
    protected $consumers = [];

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var string
     */
    protected $logFilePath;

    /**
     * @var int|null
     */
    protected $maxMessages = null;

    /**
     * @var PublisherConsumerController
     */
    protected $publisherConsumerController;

    /**
     * Workaround for https://bugs.php.net/bug.php?id=72286
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function tearDownAfterClass(): void
    {
        // phpcs:enable Magento2.Functions.StaticFunction
        if (version_compare(phpversion(), '7') == -1) {
            $closeConnection = new ReflectionMethod(Config::class, 'closeConnection');
            $closeConnection->setAccessible(true);

            $config = Bootstrap::getObjectManager()->get(Config::class);
            $closeConnection->invoke($config);
        }
    }

    /**
     * Checks that logs exist
     *
     * @param int $expectedLinesCount
     * @return bool
     */
    public function checkLogsExists($expectedLinesCount)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $actualCount = file_exists($this->logFilePath) ? count(file($this->logFilePath)) : 0;
        return $expectedLinesCount === $actualCount;
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->logFilePath = TESTS_TEMP_DIR . "/MessageQueueTestLog.txt";
        $this->publisherConsumerController = $this->objectManager->create(
            PublisherConsumerController::class,
            [
                'consumers' => $this->consumers,
                'logFilePath' => $this->logFilePath,
                'maxMessages' => $this->maxMessages,
                'appInitParams' => Bootstrap::getInstance()->getAppInitParams()
            ]
        );

        try {
            $this->publisherConsumerController->initialize();
        } catch (EnvironmentPreconditionException $e) {
            $this->markTestSkipped($e->getMessage());
        } catch (PreconditionFailedException $e) {
            $this->fail(
                $e->getMessage()
            );
        }
        $this->publisher = $this->publisherConsumerController->getPublisher();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->publisherConsumerController->stopConsumers();
    }

    /**
     * Wait for asynchronous handlers to log data to file.
     *
     * @param int $expectedLinesCount
     * @param string $logFilePath
     */
    protected function waitForAsynchronousResult($expectedLinesCount, $logFilePath)
    {
        try {
            //$expectedLinesCount, $logFilePath
            $this->publisherConsumerController->waitForAsynchronousResult(
                [$this, 'checkLogsExists'],
                [$expectedLinesCount, $logFilePath]
            );
        } catch (PreconditionFailedException $e) {
            $this->fail($e->getMessage());
        }
    }
}
