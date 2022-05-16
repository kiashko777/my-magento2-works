<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/core_fixturestore.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$currentStore = Bootstrap::getObjectManager()->get(
    StoreManagerInterface::class
)->getStore()->getId();
$otherStore = Bootstrap::getObjectManager()->get(
    StoreManagerInterface::class
)->getStore(
    'fixturestore'
)->getId();

/** @var Subscriber $subscriber */
$subscriber = Bootstrap::getObjectManager()
    ->create(Subscriber::class);
$subscriber->setStoreId($currentStore)
    ->setCustomerId(1)
    ->setSubscriberEmail('customer@example.com')
    ->setSubscriberStatus(Subscriber::STATUS_SUBSCRIBED)
    ->save();
$firstSubscriberId = $subscriber->getId();

$subscriber = Bootstrap::getObjectManager()
    ->create(Subscriber::class);
$subscriber->setStoreId($currentStore)
    // Intentionally setting ID to 0 instead of 2 to test fallback mechanism in Subscriber model
    ->setCustomerId(0)
    ->setSubscriberEmail('customer_two@example.com')
    ->setSubscriberStatus(Subscriber::STATUS_SUBSCRIBED)
    ->save();

/** @var Subscriber $subscriber */
$subscriber = Bootstrap::getObjectManager()
    ->create(Subscriber::class);
$subscriber->setStoreId($currentStore)
    ->setCustomerId(1)
    ->setSubscriberEmail('customer_confirm@example.com')
    ->setSubscriberConfirmCode('ysayquyajua23iq29gxwu2eax2qb6gvy')
    ->setSubscriberStatus(Subscriber::STATUS_UNSUBSCRIBED)
    ->save();
