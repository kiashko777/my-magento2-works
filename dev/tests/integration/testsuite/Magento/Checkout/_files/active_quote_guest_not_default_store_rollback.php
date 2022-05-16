<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store_rollback.php');

$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('test_order_1_not_default_store_guest', 'reserved_order_id')->delete();
