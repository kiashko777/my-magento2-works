<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$repository = Bootstrap::getObjectManager()->create(
    ProductRepository::class
);
try {
    $product = $repository->get('simple', false, null, true);
    $product->delete();
} catch (NoSuchEntityException $e) {
    //Entity already deleted
}

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(9)->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
