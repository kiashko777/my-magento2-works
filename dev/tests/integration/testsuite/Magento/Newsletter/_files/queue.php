<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Newsletter\Model\Queue;
use Magento\Newsletter\Model\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Newsletter/_files/template.php');
Resolver::getInstance()->requireDataFixture('Magento/Newsletter/_files/subscribers.php');

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var $template Template */
$template = $objectManager->create(Template::class);
$template->load('fixture_tpl', 'template_code');
$templateId = $template->getId();

$currentStore = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();
$otherStore = $objectManager->get(StoreManagerInterface::class)->getStore('fixturestore')->getId();

/** @var $queue Queue */
$queue = $objectManager->create(Queue::class);
$queue->setTemplateId(
    $templateId
)->setNewsletterText(
    '{{view url="images/logo.gif"}}'
)->setNewsletterSubject(
    'Subject'
)->setNewsletterSenderName(
    'CustomerSupport'
)->setNewsletterSenderEmail(
    'support@example.com'
)->setQueueStatus(
    Queue::STATUS_NEVER
)->setQueueStartAtByString(
    0
)->setStores(
    [$currentStore, $otherStore]
)->save();
