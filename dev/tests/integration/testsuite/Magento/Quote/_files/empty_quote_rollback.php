<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var $objectManager ObjectManager */

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('reserved_order_id', 'reserved_order_id')
    ->delete();

$objectManager->create(QuoteIdMask::class)
    ->delete($quote->getId());
