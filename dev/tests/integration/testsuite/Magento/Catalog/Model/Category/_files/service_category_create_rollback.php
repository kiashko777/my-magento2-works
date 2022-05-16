<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Category $category */
$category = Bootstrap::getObjectManager()->get(Category::class);
$category = $category->loadByAttribute('url_key', 'test-category-name');

if ($category && $category->getId()) {
    $category->delete();
}
