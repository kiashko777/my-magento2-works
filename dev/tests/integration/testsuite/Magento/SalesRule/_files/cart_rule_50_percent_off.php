<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);

/** @var Rule $salesRule */
$salesRule = $objectManager->create(Rule::class);
$salesRule->setData(
    [
        'name' => '50% Off on Large Orders',
        'is_active' => 1,
        'customer_group_ids' => [GroupManagement::NOT_LOGGED_IN_ID],
        'coupon_type' => Rule::COUPON_TYPE_NO_COUPON,
        'conditions' => [
            [
                'type' => Address::class,
                'attribute' => 'base_subtotal',
                'operator' => '>',
                'value' => 1000
            ]
        ],
        'simple_action' => 'by_percent',
        'discount_amount' => 50,
        'discount_step' => 0,
        'stop_rules_processing' => 1,
        'website_ids' => [
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getWebsite()->getId()
        ]
    ]
);
$salesRule->save();
$registry->unregister('Magento/SalesRule/_files/cart_rule_50_percent_off');
$registry->register('Magento/SalesRule/_files/cart_rule_50_percent_off', $salesRule->getRuleId());
