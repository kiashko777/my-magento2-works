<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

foreach (['simple1', 'simple2'] as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $exception) {
        //Products already removed
    }
}

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(333);
if ($category->getId()) {
    $category->delete();
}

$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');
if ($attribute instanceof AbstractAttribute
    && $attribute->getId()
) {
    $attribute->delete();
}
$eavConfig->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
