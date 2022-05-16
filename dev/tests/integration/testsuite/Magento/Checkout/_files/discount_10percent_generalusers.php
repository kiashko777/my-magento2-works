<?php
/**
 * SalesRule 10% discount coupon
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Rule $salesRule */
$salesRule = $objectManager->create(Rule::class);

$data = [
    'name' => 'Test Coupon for General',
    'is_active' => true,
    'store_labels' => [0 => 'Test Coupon for General'],
    'website_ids' => [
        Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore()->getWebsiteId()
    ],
    'customer_group_ids' => [1],
    'coupon_type' => Rule::COUPON_TYPE_SPECIFIC,
    'coupon_code' => '2?ds5!2d',
    'simple_action' => Rule::BY_PERCENT_ACTION,
    'discount_amount' => 10,
    'discount_step' => 1
];

$salesRule->loadPost($data)->setUseAutoGeneration(false)->save();
$objectManager->get(
    Registry::class
)->unregister('Magento/Checkout/_file/discount_10percent_generalusers');
$objectManager->get(Registry::class)
    ->register('Magento/Checkout/_file/discount_10percent_generalusers', $salesRule->getRuleId());
