<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\Consumer;

use Magento\Framework\MessageQueue\BatchConsumer;
use Magento\Framework\MessageQueue\Consumer\Config\ConsumerConfigItem\Handler\Iterator as HandlerIterator;
use Magento\Framework\MessageQueue\ConsumerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleMessageQueueConfiguration\AsyncHandler;
use Magento\TestModuleMessageQueueConfiguration\SyncHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test access to consumer configuration declared in deprecated queue.xml configs using Consumer\ConfigInterface.
 *
 * @magentoCache config disabled
 */
class DeprecatedConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetConsumerMultipleHandlersFromCommunicationConfig()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $consumer = $config->getConsumer('deprecatedConfigAsyncBoolConsumer');

        $this->assertEquals('deprecatedConfigAsyncBoolConsumer', $consumer->getName());
        $this->assertEquals('deprecated.config.queue.2', $consumer->getQueue());
        $this->assertEquals('db', $consumer->getConnection());
        $this->assertEquals(ConsumerInterface::class, $consumer->getConsumerInstance());
        $this->assertNull($consumer->getMaxMessages());

        $handlers = $consumer->getHandlers();
        $this->assertInstanceOf(HandlerIterator::class, $handlers);
        $this->assertCount(2, $handlers);
        $this->assertEquals('methodWithBoolParam', $handlers[0]->getMethod());
        $this->assertEquals(AsyncHandler::class, $handlers[0]->getType());
        $this->assertEquals('methodWithMixedParam', $handlers[1]->getMethod());
        $this->assertEquals(AsyncHandler::class, $handlers[1]->getType());
    }

    public function testGetConsumerCustomHandler()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $consumer = $config->getConsumer('deprecatedConfigAsyncMixedConsumer');

        $this->assertEquals('deprecatedConfigAsyncMixedConsumer', $consumer->getName());
        $this->assertEquals('deprecated.config.queue.3', $consumer->getQueue());
        $this->assertEquals('amqp', $consumer->getConnection());
        $this->assertEquals(ConsumerInterface::class, $consumer->getConsumerInstance());
        $this->assertNull($consumer->getMaxMessages());

        $handlers = $consumer->getHandlers();
        $this->assertInstanceOf(HandlerIterator::class, $handlers);
        $this->assertCount(1, $handlers);
        $this->assertEquals('methodWithMixedParam', $handlers[0]->getMethod());
        $this->assertEquals(AsyncHandler::class, $handlers[0]->getType());
    }

    public function testGetConsumerCustomConnectionSync()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $consumer = $config->getConsumer('deprecatedConfigSyncBoolConsumer');

        $this->assertEquals('deprecatedConfigSyncBoolConsumer', $consumer->getName());
        $this->assertEquals('deprecated.config.queue.4', $consumer->getQueue());
        $this->assertEquals('amqp', $consumer->getConnection());
        $this->assertEquals(ConsumerInterface::class, $consumer->getConsumerInstance());
        $this->assertNull($consumer->getMaxMessages());

        $handlers = $consumer->getHandlers();
        $this->assertInstanceOf(HandlerIterator::class, $handlers);
        $this->assertCount(1, $handlers);
        $this->assertEquals('methodWithBoolParam', $handlers[0]->getMethod());
        $this->assertEquals(SyncHandler::class, $handlers[0]->getType());
    }

    public function testGetConsumerCustomConsumerAndMaxMessages()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $consumer = $config->getConsumer('deprecatedConfigAsyncStringConsumer');

        $this->assertEquals('deprecatedConfigAsyncStringConsumer', $consumer->getName());
        $this->assertEquals('deprecated.config.queue.1', $consumer->getQueue());
        $this->assertEquals('amqp', $consumer->getConnection());
        $this->assertEquals(BatchConsumer::class, $consumer->getConsumerInstance());
        $this->assertEquals(200, $consumer->getMaxMessages());

        $handlers = $consumer->getHandlers();
        $this->assertInstanceOf(HandlerIterator::class, $handlers);
        $this->assertCount(0, $handlers);
    }

    public function testGetOverlapWithQueueConfig()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $consumer = $config->getConsumer('overlappingConsumerDeclaration');

        $this->assertEquals('overlappingConsumerDeclaration', $consumer->getName());
        $this->assertEquals('consumer.config.queue', $consumer->getQueue());
        $this->assertEquals('amqp', $consumer->getConnection());
        $this->assertEquals(ConsumerInterface::class, $consumer->getConsumerInstance());
        $this->assertNull($consumer->getMaxMessages());

        $handlers = $consumer->getHandlers();
        $this->assertInstanceOf(HandlerIterator::class, $handlers);
        $this->assertCount(0, $handlers);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
