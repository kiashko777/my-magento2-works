<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Creates simple Catalog Rule with the following data:
 * active, applied to all products, without time limits, with 10% off for Not Logged In Customers
 */

/** @var $banner Rule */

use Magento\CatalogRule\Model\Indexer\IndexBuilder;
use Magento\CatalogRule\Model\Rule;
use Magento\Customer\Model\GroupManagement;
use Magento\TestFramework\Helper\Bootstrap;

$catalogRule = Bootstrap::getObjectManager()->create(
    Rule::class
);

$catalogRule
    ->setIsActive(1)
    ->setName('Test Catalog Rule')
    ->setCustomerGroupIds(GroupManagement::NOT_LOGGED_IN_ID)
    ->setDiscountAmount(10)
    ->setWebsiteIds([0 => 1])
    ->setSimpleAction('by_percent')
    ->setStopRulesProcessing(false)
    ->setSortOrder(0)
    ->setSubIsEnable(0)
    ->setSubDiscountAmount(0)
    ->save();

/** @var IndexBuilder $indexBuilder */
$indexBuilder = Bootstrap::getObjectManager()
    ->get(IndexBuilder::class);
$indexBuilder->reindexFull();
sleep(1);
