<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Paypal/_files/quote_express.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Quote $quote */
$quote = $objectManager->get(QuoteFactory::class)->create();
$quote->load('100000002', 'reserved_order_id');
$quote->setCustomerEmail('admin@example.com');
/** @var $service CartManagementInterface */
$service = Bootstrap::getObjectManager()
    ->create(CartManagementInterface::class);
$order = $service->submit($quote, ['increment_id' => '100000002']);
