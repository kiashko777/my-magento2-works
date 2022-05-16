<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Customer\Model\Address;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Address $customerAddress */
$customerAddress = Bootstrap::getObjectManager()
    ->create(Address::class);
$customerAddress->load(1);
if ($customerAddress->getId()) {
    $customerAddress->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
