<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $website Website */

use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$website = Bootstrap::getObjectManager()->create(Website::class);
$website->setData(['code' => 'test', 'name' => 'Test Website', 'default_group_id' => '1', 'is_default' => '0']);
$website->save();

$objectManager = Bootstrap::getObjectManager();
/* Refresh stores memory cache */
$objectManager->get('Magento\Store\Model\StoreManagerInterface')->reinitStores();
