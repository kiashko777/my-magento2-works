<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\Topology;

use Magento\Framework\MessageQueue\Topology\Config\ExchangeConfigItem\BindingInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test of queue topology configuration reading and parsing.
 *
 * @magentoCache config disabled
 */
class ConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetExchangeByName()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $exchange = $config->getExchange('magento-topic-based-exchange1', 'amqp');
        $this->assertEquals('magento-topic-based-exchange1', $exchange->getName());
        $this->assertEquals('topic', $exchange->getType());
        $this->assertEquals('amqp', $exchange->getConnection());
        $exchangeArguments = $exchange->getArguments();
        $expectedArguments = ['alternate-exchange' => 'magento-log-exchange'];
        $this->assertEquals($expectedArguments, $exchangeArguments);

        /** @var BindingInterface $binding */
        $binding = current($exchange->getBindings());
        $this->assertEquals('topicBasedRouting1', $binding->getId());
        $this->assertEquals('anotherTopic1', $binding->getTopic());
        $this->assertEquals('queue', $binding->getDestinationType());
        $this->assertEquals('topic-queue1', $binding->getDestination());
        $bindingArguments = $binding->getArguments();
        $expectedArguments = ['argument1' => 'value'];
        $this->assertEquals($expectedArguments, $bindingArguments);
    }

    public function testGetExchangeByNameWithDefaultValues()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $exchange = $config->getExchange('magento-topic-based-exchange2', 'amqp');
        $this->assertEquals('magento-topic-based-exchange2', $exchange->getName());
        $this->assertEquals('topic', $exchange->getType());
        $this->assertEquals('amqp', $exchange->getConnection());
        $exchangeArguments = $exchange->getArguments();
        $expectedArguments = [
            'alternate-exchange' => 'magento-log-exchange',
            'arrayValue' => [
                'element01' => '10',
                'element02' => '20',
            ]
        ];
        $this->assertEquals($expectedArguments, $exchangeArguments);

        /** @var BindingInterface $binding */
        $binding = current($exchange->getBindings());
        $this->assertEquals('topicBasedRouting2', $binding->getId());
        $this->assertEquals('anotherTopic2', $binding->getTopic());
        $this->assertEquals('queue', $binding->getDestinationType());
        $this->assertEquals('topic-queue2', $binding->getDestination());
        $bindingArguments = $binding->getArguments();
        $expectedArguments = ['argument1' => 'value', 'argument2' => true, 'argument3' => 150];
        $this->assertEquals($expectedArguments, $bindingArguments);
    }

    public function testGetAllExchanges()
    {
        /** @var ConfigInterface $config */
        $config = $this->objectManager->create(ConfigInterface::class);
        $exchanges = $config->getExchanges();
        $expectedResults = ['magento-topic-based-exchange1', 'magento-topic-based-exchange2'];
        $actual = [];
        foreach ($exchanges as $exchange) {
            $actual[] = $exchange->getName();
        }
        $this->assertEmpty(array_diff($expectedResults, $actual));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
