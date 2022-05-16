<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(28767);
if ($category->getId()) {
    $category->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
