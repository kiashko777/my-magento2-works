<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$eavSetupFactory = $objectManager->create(EavSetupFactory::class);
/** @var EavSetup $eavSetup */
$eavSetup = $eavSetupFactory->create();
$eavSetup->updateAttribute(
    Product::ENTITY,
    'quantity_and_stock_status',
    [
        'is_used_in_grid' => 0,
    ]
);
