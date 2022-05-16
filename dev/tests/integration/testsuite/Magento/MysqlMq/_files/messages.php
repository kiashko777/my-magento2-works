<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\MysqlMq\Model\MessageFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var MessageFactory $messageFactory */
$messageFactory = $objectManager->create(MessageFactory::class);
$message = $messageFactory->create();

$message->setTopicName('topic.updated.use.just.in.tests')
    ->setBody('{test:test}')
    ->save();

$message = $messageFactory->create();

$message->setTopicName('topic_second.updated.use.just.in.tests')
    ->setBody('{test:test}')
    ->save();

$message = $messageFactory->create();

$message->setTopicName('topic_thrird.updated.use.just.in.tests')
    ->setBody('{test:test}')
    ->save();
