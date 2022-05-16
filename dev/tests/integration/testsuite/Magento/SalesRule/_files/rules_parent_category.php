<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product;
use Magento\SalesRule\Model\Rule\Condition\Product\Subselect;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
/** @var Rule $rule */
$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
$salesRule->setData(
    [
        'name' => '50% Off on Configurable parent category',
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
                'type' => Subselect::class,
                'attribute' => 'qty',
                'operator' => '==',
                'value' => '1',
                'is_value_processed' => null,
                'aggregator' => 'all',
                'conditions' =>
                    [
                        [
                            'type' => Product::class,
                            'attribute' => 'category_ids',
                            'attribute_scope' => 'parent',
                            'operator' => '!=',
                            'value' => '2',
                            'is_value_processed' => false,
                        ],
                    ],
            ],
        ],
]);

$salesRule->save();
$registry->unregister('50% Off on Configurable parent category');
$registry->register('50% Off on Configurable parent category', $salesRule->getRuleId());
