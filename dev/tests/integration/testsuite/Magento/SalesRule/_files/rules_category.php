<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Rule $rule */

use Magento\Catalog\Model\Category;
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
        'stop_rules_processing' => 0,
        'website_ids' => [
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getWebsite()->getId()
        ],
        'store_labels' => [

            'store_id' => 0,
            'store_label' => 'TestRule_Label',

        ]
    ]
);

$salesRule->getConditions()->loadArray(
    [
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
                                'value' => '66',
                                'is_value_processed' => false,
                            ],
                        ],
                ],
            ],
    ]
);

$salesRule->save();

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    66
)->setCreatedAt(
    '2014-06-23 09:50:07'
)->setName(
    'Category 1'
)->setParentId(
    2
)->setPath(
    '1/2/66'
)->setLevel(
    2
)->setAvailableSortBy(
    ['position', 'name']
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->save();

/** @var Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('_fixture/Magento_SalesRule_Category');
$registry->register('_fixture/Magento_SalesRule_Category', $salesRule);
