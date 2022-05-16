<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

/** @var AttributeRepositoryInterface $repository */

use Magento\Customer\Model\GroupManagement;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product;
use Magento\SalesRule\Model\Rule\Condition\Product\Found;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$repository = Bootstrap::getObjectManager()
    ->create(AttributeRepositoryInterface::class);

/** @var AttributeInterface $skuAttribute */
$skuAttribute = $repository->get(
    'catalog_product',
    'sku'
);
$data = $skuAttribute->getData();
$data['is_used_for_promo_rules'] = 1;
$skuAttribute->setData($data);
$skuAttribute->save();

/** @var Rule $rule */
$salesRule = Bootstrap::getObjectManager()->create(Rule::class);
$salesRule->setData(
    [
        'name' => '20% Off',
        'is_active' => 1,
        'customer_group_ids' => [GroupManagement::NOT_LOGGED_IN_ID],
        'coupon_type' => Rule::COUPON_TYPE_NO_COUPON,
        'simple_action' => 'by_percent',
        'discount_amount' => 20,
        'discount_step' => 0,
        'stop_rules_processing' => 1,
        'website_ids' => [
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getWebsite()->getId(),
        ],
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
                            'attribute' => 'sku',
                            'operator' => '!=',
                            'value' => 'product-bundle',
                            'is_value_processed' => false,
                        ],
                    ],
            ],
        ],
]);

$salesRule->save();

/** @var Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('_fixture/Magento_SalesRule_Sku_Exclude');
$registry->register('_fixture/Magento_SalesRule_Sku_Exclude', $salesRule);
