<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $rule */

use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$tomorrow = new DateTime();
$tomorrow->add(DateInterval::createFromDateString('+1 day'));

$rule = Bootstrap::getObjectManager()->create(Rule::class);
$rule->setName(
    '#1'
)->setIsActive(
    1
)->setStopRulesProcessing(
    0
)->setIsAdvanced(
    1
)->setCouponType(
    Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON
)->setUseAutoGeneration(
    0
)->setWebsiteIds(
    '1'
)->setCustomerGroupIds(
    '0'
)->setDiscountStep(
    0
)->setFromDate(
    date('Y-m-d')
)->setToDate(
    $tomorrow->format('Y-m-d')
)->save();
