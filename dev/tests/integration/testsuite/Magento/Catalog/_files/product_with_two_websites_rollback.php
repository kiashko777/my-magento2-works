<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager
    ->get(Registry::class);
$registry->unregister("isSecureArea");
$registry->register("isSecureArea", true);

/** @var Magento\Store\Model\Website $website */
$website = $objectManager->get(Magento\Store\Model\Website::class);
$website->load('second_website', 'code');
if ($website->getId()) {
    $website->delete();
}

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

try {
    $firstProduct = $productRepository->get('unique-simple-azaza');
    $productRepository->delete($firstProduct);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

$objectManager->get(StoreManagerInterface::class)->reinitStores();
