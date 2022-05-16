<?php
/**
 * Rollback for quote_with_virtual_product_and_address.php fixture.
 *
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
$quote->load('test_order_with_virtual_product', 'reserved_order_id')->delete();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMask::class);
$quoteIdMask->delete($quote->getId());

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual_rollback.php');
