<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\ResourceModel\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/dropdown_attribute_rollback.php');
/**
 * Remove all products as strategy of isolation process
 */
$registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $productCollection Product */
$productCollection = Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product')
    ->getCollection();

foreach ($productCollection as $product) {
    $product->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
