<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/category_duplicates.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Category $category */
$categoryModel = $objectManager->create(Category::class);
$categoryModel->setStoreId(Store::DEFAULT_STORE_ID);

$categoryModel->load(444)
    ->setName('Category 2-updated')
    ->save();
