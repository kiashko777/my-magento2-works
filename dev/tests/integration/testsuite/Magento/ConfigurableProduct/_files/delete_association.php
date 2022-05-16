<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->load(1);
$product->setAssociatedProductIds([20]);
$product->save();
