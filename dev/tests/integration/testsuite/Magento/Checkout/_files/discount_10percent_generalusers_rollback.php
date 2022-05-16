<?php
/**
 * SalesRule 10% discount coupon
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $salesRule */

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
/** @var int $salesRuleId */
$salesRuleId = Bootstrap::getObjectManager()->get(Registry::class)
    ->registry('Magento/Checkout/_file/discount_10percent_generalusers');
$salesRule->load($salesRuleId);
$salesRule->delete();
