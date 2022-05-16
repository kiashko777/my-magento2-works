<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\Publisher;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\Publisher\Config\PublisherConnectionInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test of queue publisher configuration reading and parsing.
 *
 * @magentoCache config disabled
 */
class ConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetPublishersWithOneEnabledConnection()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);

        $publishers = $config->getPublishers();
        $publisher = $config->getPublisher('topic.message.queue.config.01');
        $itemFromList = null;
        foreach ($publishers as $item) {
            if ($item->getTopic() == 'topic.message.queue.config.01') {
                $itemFromList = $item;
                break;
            }
        }

        $this->assertEquals($publisher, $itemFromList, 'Inconsistent publisher object');

        $this->assertEquals('topic.message.queue.config.01', $publisher->getTopic(), 'Incorrect topic name');
        $this->assertFalse($publisher->isDisabled(), 'Incorrect publisher state');
        /** @var PublisherConnectionInterface $connection */
        $connection = $publisher->getConnection();
        $this->assertEquals('amqp', $connection->getName(), 'Incorrect connection name');
        $this->assertEquals('magento2', $connection->getExchange(), 'Incorrect exchange name');
        $this->assertFalse($connection->isDisabled(), 'Incorrect connection status');
    }

    public function testGetPublisherConnectionWithoutConfiguredExchange()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);

        $publisher = $config->getPublisher('topic.message.queue.config.04');
        $connection = $publisher->getConnection();
        $this->assertEquals('magento', $connection->getExchange(), 'Incorrect exchange name');
    }

    public function testGetPublishersWithoutEnabledConnection()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);

        $publisher = $config->getPublisher('topic.message.queue.config.02');

        $this->assertEquals('topic.message.queue.config.02', $publisher->getTopic(), 'Incorrect topic name');
        $this->assertFalse($publisher->isDisabled(), 'Incorrect publisher state');

        /** @var PublisherConnectionInterface $connection */
        $connection = $publisher->getConnection();
        $this->assertEquals('amqp', $connection->getName(), 'Incorrect default connection name');
        $this->assertEquals('magento', $connection->getExchange(), 'Incorrect default exchange name');
        $this->assertFalse($connection->isDisabled(), 'Incorrect connection status');
    }

    /**
     */
    public function testGetDisabledPublisherThrowsException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Publisher \'topic.message.queue.config.03\' is not declared.');

        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $config->getPublisher('topic.message.queue.config.03');
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
