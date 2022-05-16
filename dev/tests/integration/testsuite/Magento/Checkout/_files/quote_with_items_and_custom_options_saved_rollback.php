<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product\Option\Type\File\ValidatorFile;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(ValidatorFile::class);

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->load('test_order_item_with_items_and_custom_options', 'reserved_order_id');
$quoteId = $quote->getId();
if ($quote->getId()) {
    $objectManager->get(QuoteRepository::class)->delete($quote);
}

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/quote_with_address_rollback.php');
