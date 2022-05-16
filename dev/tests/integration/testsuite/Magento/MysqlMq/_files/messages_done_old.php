<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\MysqlMq\Model\MessageFactory;
use Magento\MysqlMq\Model\MessageStatusFactory;
use Magento\MysqlMq\Model\QueueFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var MessageFactory $messageFactory */
$messageFactory = $objectManager->create(MessageFactory::class);
$message1 = $messageFactory->create()
    ->load('topic.updated.use.just.in.tests', 'topic_name');

$messageId1 = $message1->getId();

/** @var MessageStatusFactory $messageStatusFactory */
$queueFactory = $objectManager->create(QueueFactory::class);
$queueId1 = $queueFactory->create()
    ->load('queue1', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId2 = $queueFactory->create()
    ->load('queue2', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId3 = $queueFactory->create()
    ->load('queue3', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId4 = $queueFactory->create()
    ->load('queue4', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();

$plan = [
    [
        $messageId1,
        $queueId1,
        time() - 1 - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_COMPLETE
    ],
    [
        $messageId1,
        $queueId2,
        time() - 1 - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_ERROR
    ],
    [
        $messageId1,
        $queueId3,
        time() - 1 - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_COMPLETE
    ],
];

/** @var MessageStatusFactory $messageStatusFactory */
$messageStatusFactory = $objectManager->create(MessageStatusFactory::class);
foreach ($plan as $instruction) {
    $messageStatus = $messageStatusFactory->create();

    $messageStatus->setQueueId($instruction[1])
        ->setMessageId($instruction[0])
        ->setUpdatedAt($instruction[2])
        ->setStatus($instruction[3])
        ->save();
}
