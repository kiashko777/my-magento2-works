<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\Publisher;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test access to publisher configuration declared in deprecated queue.xml configs using Publisher\ConfigInterface.
 *
 * @magentoCache config disabled
 */
class DeprecatedConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetPublisher()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $publisher = $config->getPublisher('deprecated.config.async.string.topic');
        $this->assertEquals('deprecated.config.async.string.topic', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals('amqp', $connection->getName());
        $this->assertEquals('magento', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }

    public function testGetPublisherCustomConnection()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $publisher = $config->getPublisher('deprecated.config.sync.bool.topic');
        $this->assertEquals('deprecated.config.sync.bool.topic', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals('amqp', $connection->getName());
        $this->assertEquals('customExchange', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }

    public function testGetOverlapWithQueueConfig()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $publisher = $config->getPublisher('overlapping.topic.declaration');
        $this->assertEquals('overlapping.topic.declaration', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals('amqp', $connection->getName());
        $this->assertEquals('magento', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
