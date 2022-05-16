<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MysqlMq\Model\Driver;

use Magento\Framework\MessageQueue\Config\Data;
use Magento\Framework\MessageQueue\EnvelopeFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for MySQL queue driver class.
 *
 * @magentoDbIsolation disabled
 */
class QueueTest extends TestCase
{
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     */
    public function testPushAndDequeue()
    {
        /** @var EnvelopeFactory $envelopFactory */
        $envelopFactory = $this->objectManager->get(EnvelopeFactory::class);
        $messageBody = '{"data": {"body": "Message body"}, "message_id": 1}';
        $topicName = 'some.topic';
        $envelop = $envelopFactory->create(['body' => $messageBody, 'properties' => ['topic_name' => $topicName]]);

        $this->queue->push($envelop);

        $messageFromQueue = $this->queue->dequeue();

        $this->assertEquals($messageBody, $messageFromQueue->getBody());
        $actualMessageProperties = $messageFromQueue->getProperties();
        $this->assertArrayHasKey('topic_name', $actualMessageProperties);
        $this->assertEquals($topicName, $actualMessageProperties['topic_name']);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var Data $queueConfig */
        $queueConfig = $this->objectManager->get(Data::class);
        $queueConfig->reset();

        $this->queue = $this->objectManager->create(
            Queue::class,
            ['queueName' => 'queue2']
        );
    }

    protected function tearDown(): void
    {
        /** @var Data $queueConfig */
        $queueConfig = $this->objectManager->get(Data::class);
        $queueConfig->reset();
    }
}
