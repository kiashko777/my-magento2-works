<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('reserved_order_id_1', 'reserved_order_id')->delete();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMask::class);
$quoteIdMask->delete($quote->getId());

Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable_rollback.php');
