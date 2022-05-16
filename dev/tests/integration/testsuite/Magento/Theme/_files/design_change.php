<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Design;

$storeId = Bootstrap::getObjectManager()->get(
    StoreManagerInterface::class
)->getDefaultStoreView()->getId();
/** @var $change Design */
$change = Bootstrap::getObjectManager()->create(Design::class);
$change->setStoreId($storeId)->setDesign('Magento/luma')->setDateFrom('2001-01-01 01:01:01')->save();
