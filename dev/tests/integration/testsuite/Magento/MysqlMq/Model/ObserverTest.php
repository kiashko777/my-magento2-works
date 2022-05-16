<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MysqlMq\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\MysqlMq\Model\ResourceModel\MessageCollectionFactory;
use Magento\MysqlMq\Model\ResourceModel\MessageStatusCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ObserverTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Observer
     */
    private $observer;

    /**
     * @var QueueManagement
     */
    private $queueManagement;

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages_done_old.php
     */
    public function testCleanUpOld()
    {
        /** @var MessageStatusCollectionFactory $messageStatusCollectionFactory */
        $messageStatusCollectionFactory = $this->objectManager
            ->create(MessageStatusCollectionFactory::class);

        /** @var MessageCollectionFactory $messageStatusCollectionFactory */
        $messageCollectionFactory = $this->objectManager
            ->create(MessageCollectionFactory::class);

        //Check how many messages in collection by the beginning of tests
        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic.updated.use.just.in.tests');
        $this->assertEquals(1, $messageCollection->getSize());
        $messageId = $messageCollection->getFirstItem()->getId();

        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId);
        $this->assertEquals(3, $messageStatusCollection->getSize());

        //Run clean up once. It should move 3 out of 4 statuses to TO BE DELETED status
        $this->observer->cleanupMessages();

        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic.updated.use.just.in.tests');
        $this->assertEquals(0, $messageCollection->getSize());
        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId);
        $this->assertEquals(0, $messageStatusCollection->getSize());
    }

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages_done_old.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages_done_recent.php
     */
    public function testCleanupMessages()
    {
        /** @var MessageStatusCollectionFactory $messageStatusCollectionFactory */
        $messageStatusCollectionFactory = $this->objectManager
            ->create(MessageStatusCollectionFactory::class);

        /** @var MessageCollectionFactory $messageStatusCollectionFactory */
        $messageCollectionFactory = $this->objectManager
            ->create(MessageCollectionFactory::class);

        //Check how many messages in collection by the beginning of tests
        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic.updated.use.just.in.tests');
        $this->assertEquals(1, $messageCollection->getSize());
        $messageId = $messageCollection->getFirstItem()->getId();

        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId);
        $this->assertEquals(4, $messageStatusCollection->getSize());

        //Run clean up once. It should move 3 out of 4 statuses to TO BE DELETED status
        $this->observer->cleanupMessages();

        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic.updated.use.just.in.tests');
        $this->assertEquals(1, $messageCollection->getSize());

        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId)
            ->addFieldToFilter('status', QueueManagement::MESSAGE_STATUS_TO_BE_DELETED);

        $this->assertEquals(3, $messageStatusCollection->getSize());

        // Change the Updated At in order to make job visible
        $lastMessageStatus = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId)
            ->addFieldToFilter('status', QueueManagement::MESSAGE_STATUS_COMPLETE)
            ->getFirstItem();
        $lastMessageStatus->setUpdatedAt(time() - 1 - 24 * 7 * 60 * 60)
            ->save();

        $this->observer->cleanupMessages();

        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic.updated.use.just.in.tests');
        $this->assertEquals(0, $messageCollection->getSize());
        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId);
        $this->assertEquals(0, $messageStatusCollection->getSize());
    }

    /**
     * @magentoDataFixture Magento/MysqlMq/_files/queues.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages.php
     * @magentoDataFixture Magento/MysqlMq/_files/messages_in_progress.php
     */
    public function testCleanupInProgressMessages()
    {
        /** @var MessageStatusCollectionFactory $messageStatusCollectionFactory */
        $messageStatusCollectionFactory = $this->objectManager
            ->create(MessageStatusCollectionFactory::class);

        /** @var MessageCollectionFactory $messageStatusCollectionFactory */
        $messageCollectionFactory = $this->objectManager
            ->create(MessageCollectionFactory::class);

        //Check how many messages in collection by the beginning of tests
        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic_second.updated.use.just.in.tests');
        $this->assertEquals(1, $messageCollection->getSize());
        $messageId = $messageCollection->getFirstItem()->getId();

        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId);
        $this->assertEquals(2, $messageStatusCollection->getSize());

        $this->observer->cleanupMessages();

        $messageCollection = $messageCollectionFactory->create()
            ->addFieldToFilter('topic_name', 'topic_second.updated.use.just.in.tests');
        $this->assertEquals(1, $messageCollection->getSize());
        $messageStatusCollection = $messageStatusCollectionFactory->create()
            ->addFieldToFilter('message_id', $messageId)
            ->addFieldToFilter('status', QueueManagement::MESSAGE_STATUS_RETRY_REQUIRED);
        $this->assertEquals(1, $messageStatusCollection->getSize());
        $this->assertEquals(1, $messageStatusCollection->getFirstItem()->getNumberOfTrials());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->observer = $this->objectManager->get(Observer::class);
        $this->queueManagement = $this->objectManager->get(QueueManagement::class);
    }
}
