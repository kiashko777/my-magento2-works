<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/SalesRule/_files/rules_rollback.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(56);
if ($category->getId()) {
    $category->delete();
}
