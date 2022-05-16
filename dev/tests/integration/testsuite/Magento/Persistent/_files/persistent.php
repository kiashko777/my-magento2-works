<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\CustomerRegistry;
use Magento\Persistent\Model\Session;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address.php');

/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var Session $persistentSession */
$persistentSession = Bootstrap::getObjectManager()->get(
    Session::class
);
$persistentSession->setCustomerId($customer->getId())->save();
