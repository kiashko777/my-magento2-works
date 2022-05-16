<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\GroupManagement;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Address;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Rule $rule */
$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
$salesRule->setData(
    [
        'name' => '50% Off on Checkmo Payment Method',
        'is_active' => 1,
        'customer_group_ids' => [GroupManagement::NOT_LOGGED_IN_ID],
        'coupon_type' => Rule::COUPON_TYPE_NO_COUPON,
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

$salesRule->getConditions()->loadArray([
    'type' => Combine::class,
    'attribute' => null,
    'operator' => null,
    'value' => '1',
    'is_value_processed' => null,
    'aggregator' => 'any',
    'conditions' =>
        [
            [
                'type' => Address::class,
                'attribute' => 'payment_method',
                'operator' => '==',
                'value' => 'checkmo'
            ],
        ],
]);

$salesRule->save();
