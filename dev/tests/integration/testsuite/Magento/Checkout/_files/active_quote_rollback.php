<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('test_order_1', 'reserved_order_id')->delete();
