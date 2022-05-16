<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product.php');

/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = Bootstrap::getObjectManager()
    ->create(CategoryLinkManagementInterface::class);

$categoryLinkManagement->assignProductToCategories(
    'bundle-product',
    [2]
);
