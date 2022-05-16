<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/* @var \Magento\Eav\Model\Entity\Attribute $attribute */
$attribute = $objectManager->get(\Magento\Eav\Model\Entity\Attribute::class);
$attribute->loadByCode(Product::ENTITY, 'fixed_product_attribute');
$attribute->delete();
