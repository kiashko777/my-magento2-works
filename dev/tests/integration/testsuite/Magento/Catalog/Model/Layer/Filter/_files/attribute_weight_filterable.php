<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $installer CategorySetup */

use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);

$installer->updateAttribute('catalog_product', 'weight', 'is_filterable', 1);
