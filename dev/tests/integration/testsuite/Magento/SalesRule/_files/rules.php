<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $rule1 */

use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$rule1 = Bootstrap::getObjectManager()->create(Rule::class);
$rule1->setName(
    '#1'
)->setIsActive(
    1
)->setStopRulesProcessing(
    0
)->setIsAdvanced(
    1
)->setCouponType(
    Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
)->setUseAutoGeneration(
    0
)->setWebsiteIds(
    '1'
)->setCustomerGroupIds(
    '0'
)->setDiscountStep(
    0
)->setSortOrder(1)
    ->save();

/** @var Rule $rule2 */
$rule2 = Bootstrap::getObjectManager()->create(Rule::class);
$rule2->setName(
    '#2'
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
)->setSortOrder(2)
    ->save();

/** @var Rule $rule3 */
$rule3 = Bootstrap::getObjectManager()->create(Rule::class);
$rule3->setName(
    '#3'
)->setIsActive(
    1
)->setStopRulesProcessing(
    0
)->setIsAdvanced(
    1
)->setCouponType(
    Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
)->setUseAutoGeneration(
    1
)->setWebsiteIds(
    '1'
)->setCustomerGroupIds(
    '0'
)->setDiscountStep(
    0
)->setSortOrder(3)
    ->save();

/** @var Rule $rule4 */
$rule4 = Bootstrap::getObjectManager()->create(Rule::class);
$rule4->setName(
    '#4'
)->setIsActive(
    1
)->setStopRulesProcessing(
    0
)->setIsAdvanced(
    1
)->setCouponType(
    Magento\SalesRule\Model\Rule::COUPON_TYPE_AUTO
)->setUseAutoGeneration(
    0
)->setWebsiteIds(
    '1'
)->setCustomerGroupIds(
    '0'
)->setDiscountStep(
    0
)->setSortOrder(4)
    ->save();

/** @var Rule $rule5 */
$rule5 = Bootstrap::getObjectManager()->create(Rule::class);
$rule5->setName(
    '#5'
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
)->setSortOrder(5)
    ->save();
