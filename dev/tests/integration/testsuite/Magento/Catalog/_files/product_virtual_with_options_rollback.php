<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$repository = Bootstrap::getObjectManager()->create(
    ProductRepository::class
);
try {
    $product = $repository->get('virtual', false, null, true);
    $repository->delete($product);
} catch (NoSuchEntityException $e) {
    //Entity already deleted
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
