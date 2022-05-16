<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $rule */

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$rule = Bootstrap::getObjectManager()->create(Rule::class);
$rule->setName(
    'AUTO_RULE'
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
)->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('_fixture/Magento_SalesRule_Api_RuleRepository');
$registry->register('_fixture/Magento_SalesRule_Api_RuleRepository', $rule);
