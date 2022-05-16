<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

/** @var Registry $registry */

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Collection $collection */
$collection = $objectManager->create(Collection::class);
$collection->addAttributeToSelect('id')->load();
if ($collection->count() > 0) {
    $collection->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

/** @var ProductAttributeRepositoryInterface $productAttributeRepository */
$productAttributeRepository = $objectManager->get(
    ProductAttributeRepositoryInterface::class
);

$nameAttribute = $productAttributeRepository->get('name');
$nameAttribute->setSearchWeight(5);
$productAttributeRepository->save($nameAttribute);

$descriptionAttribute = $productAttributeRepository->get('description');
$descriptionAttribute->setSearchWeight(1);
$productAttributeRepository->save($descriptionAttribute);
