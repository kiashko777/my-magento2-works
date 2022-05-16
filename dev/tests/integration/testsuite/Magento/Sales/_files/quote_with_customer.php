<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/quote.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

/** @var $quote Quote */
$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->load('test01', 'reserved_order_id');
/** @var CustomerRepositoryInterface $customer */
$customerRepository = Bootstrap::getObjectManager()
    ->create(CustomerRepositoryInterface::class);
$customerId = 1;
$customer = $customerRepository->getById($customerId);
$quote->setCustomer($customer)->setCustomerIsGuest(false)->save();
foreach ($quote->getAllAddresses() as $address) {
    $address->setCustomerId($customerId)->save();
}

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = Bootstrap::getObjectManager()
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
