<?php
/**
 * Rollback for quote_with_address_saved.php fixture.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product_with_multiple_options_rollback.php');

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('test_order_bundle', 'reserved_order_id')->delete();
