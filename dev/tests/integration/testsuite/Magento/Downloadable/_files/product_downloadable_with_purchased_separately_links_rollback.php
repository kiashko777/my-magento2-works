<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Downloadable\Api\DomainManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var DomainManagerInterface $domainManager */
$domainManager = Bootstrap::getObjectManager()->get(DomainManagerInterface::class);
$domainManager->removeDomains(['example.com']);

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get(
        'downloadable-product-with-purchased-separately-links',
        false,
        null,
        true
    );
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
    // Tests which are wrapped with MySQL transaction clear all data by transaction rollback
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
