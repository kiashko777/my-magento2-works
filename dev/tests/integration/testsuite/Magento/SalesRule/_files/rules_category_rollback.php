<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Magento\Framework\Registry $registry */

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

/** @var Magento\SalesRule\Model\Rule $rule */
$rule = $registry->registry('_fixture/Magento_SalesRule_Category');

$rule->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(66);
if ($category->getId()) {
    $category->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
