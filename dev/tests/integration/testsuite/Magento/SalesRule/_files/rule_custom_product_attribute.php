<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\GroupManagement;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product;
use Magento\SalesRule\Model\Rule\Condition\Product\Found;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$entityTypeId = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)
    ->loadByCode('catalog_category')
    ->getId();

$attributeData = [
    'attribute_code' => 'attribute_for_sales_rule_1',
    'entity_type_id' => $entityTypeId,
    'backend_type' => 'varchar',
    'is_required' => 1,
    'is_user_defined' => 1,
    'is_unique' => 0,
    'is_used_for_promo_rules' => 1,
];

/** @var \Magento\Eav\Model\Entity\Attribute $attribute */
$attribute = $objectManager->create(\Magento\Eav\Model\Entity\Attribute::class);
$attribute->setData($attributeData);
$attribute->save();

/** @var Rule $rule */
$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
$salesRule->setData(
    [
        'name' => '50% Off on some attribute',
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
    'conditions' => [
        [
            'type' => Found::class,
            'attribute' => null,
            'operator' => null,
            'value' => '0',
            'is_value_processed' => null,
            'aggregator' => 'all',
            'conditions' => [
                [
                    'type' => Product::class,
                    'attribute' => 'attribute_for_sales_rule_1',
                    'operator' => '==',
                    'value' => '2',
                    'is_value_processed' => false,
                ],
            ],
        ],
    ],
]);

$salesRule->save();
