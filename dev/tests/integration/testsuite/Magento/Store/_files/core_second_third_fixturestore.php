<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Website $website */

use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$website = Bootstrap::getObjectManager()->create(Website::class);
$website->setName('Second Website')->setCode('secondwebsite')->save();

$websiteId = $website->getId();
$groupId = $website->getDefaultGroupId();

/** @var Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->setCode('secondstore')->setWebsiteId($websiteId)->setName('Second Store')->setSortOrder(10)->setIsActive(1);
$store->save();

/** @var Website $website */
$website = Bootstrap::getObjectManager()->create(Website::class);
$website->setName('Third Website')->setCode('thirdwebsite')->save();

$websiteId = $website->getId();
$groupId = $website->getDefaultGroupId();

/** @var Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->setCode('thirdstore')->setWebsiteId($websiteId)->setName('Third Store')->setSortOrder(10)->setIsActive(1);
$store->save();
