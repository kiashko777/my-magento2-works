<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Downloadable\Api\DomainManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->getInstance()->reinitialize();

$objectManager = Bootstrap::getObjectManager();

/** @var DomainManagerInterface $domainManager */
$domainManager = $objectManager->get(DomainManagerInterface::class);
$domainManager->removeDomains(
    [
        'example.com',
        'www.example.com',
        'www.sample.example.com',
        'google.com'
    ]
);

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('downloadable-product', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) { // @codingStandardsIgnoreLine
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
