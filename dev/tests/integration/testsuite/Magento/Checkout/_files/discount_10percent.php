<?php
/**
 * SalesRule 10% discount coupon
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var RuleFactory $salesRule */
$salesRuleFactory = $objectManager->get(RuleFactory::class);

/** @var Rule $salesRule */
$salesRule = $salesRuleFactory->create();

$data = [
    'name' => 'Test Coupon',
    'is_active' => true,
    'website_ids' => [
        Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore()->getWebsiteId()
    ],
    'customer_group_ids' => [GroupManagement::NOT_LOGGED_IN_ID, 1],
    'coupon_type' => Rule::COUPON_TYPE_SPECIFIC,
    'coupon_code' => uniqid(),
    'simple_action' => Rule::BY_PERCENT_ACTION,
    'discount_amount' => 10,
    'discount_step' => 1
];

$salesRule->loadPost($data)->setUseAutoGeneration(false)->save();
$objectManager->get(Registry::class)->unregister('Magento/Checkout/_file/discount_10percent');
$objectManager->get(Registry::class)
    ->register('Magento/Checkout/_file/discount_10percent', $salesRule->getRuleId());
