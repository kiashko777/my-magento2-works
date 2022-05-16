<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

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
$quote->load('test_cart_with_bundle_and_options', 'reserved_order_id');
$quote->delete();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMask::class);
$quoteIdMask->delete($quote->getId());

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product_with_multiple_options_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
