<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

/** @var AttributeRepositoryInterface $repository */

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$repository = Bootstrap::getObjectManager()
    ->create(AttributeRepositoryInterface::class);

/** @var AttributeInterface $skuAttribute */
$skuAttribute = $repository->get(
    'catalog_product',
    'sku'
);
$data = $skuAttribute->getData();
$data['is_used_for_promo_rules'] = 0;
$skuAttribute->setData($data);
$skuAttribute->save();

/** @var Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

/** @var Magento\SalesRule\Model\Rule $rule */
$rule = $registry->registry('_fixture/Magento_SalesRule_Sku_Exclude');

$rule->delete();
