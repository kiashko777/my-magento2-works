<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $rule */

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product;
use Magento\SalesRule\Model\Rule\Condition\Product\Found;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
$salesRule->setData(
    [
        'name' => '50% Off on Large Orders',
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
    'aggregator' => 'all',
    'conditions' =>
        [
            [
                'type' => Found::class,
                'attribute' => null,
                'operator' => null,
                'value' => '1',
                'is_value_processed' => null,
                'aggregator' => 'all',
                'conditions' =>
                    [
                        [
                            'type' => Product::class,
                            'attribute' => 'category_ids',
                            'operator' => '==',
                            'value' => '2',
                            'is_value_processed' => false,
                        ],
                        [
                            'type' => Product::class,
                            'attribute' => 'attribute_set_id',
                            'operator' => '==',
                            'value' => '4',
                            'is_value_processed' => false,
                        ],
                    ],
            ],
            [
                'type' => Found::class,
                'attribute' => null,
                'operator' => null,
                'value' => '1',
                'is_value_processed' => null,
                'aggregator' => 'all',
                'conditions' =>
                    [
                        [
                            'type' => Product::class,
                            'attribute' => 'quote_item_price',
                            'operator' => '==',
                            'value' => '80',
                            'is_value_processed' => false,
                        ],
                        [
                            'type' => Product::class,
                            'attribute' => 'category_ids',
                            'operator' => '==',
                            'value' => '3',
                            'is_value_processed' => false,
                        ],
                    ],
            ],

        ],
]);

$salesRule->save();

/** @var Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('_fixture/Magento_SalesRule_Group_Multiple_Categories');
$registry->register('_fixture/Magento_SalesRule_Group_Multiple_Categories', $salesRule);
