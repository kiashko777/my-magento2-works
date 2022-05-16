<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$quote = $objectManager->create(Quote::class);
$quote->load('test_cart_with_configurable', 'reserved_order_id');
$quote->delete();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMask::class);
$quoteIdMask->delete($quote->getId());

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/product_configurable_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
