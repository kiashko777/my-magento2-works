<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

/** @var CategoryFactory $factory */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$factory = Bootstrap::getObjectManager()->create(
    CategoryFactory::class
);
/** @var CategoryRepository $repository */
$repository = Bootstrap::getObjectManager()->create(
    CategoryRepository::class
);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->create(
    StoreManagerInterface::class
);
/** @var Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('second_category_store', 'code')->getId()) {
    $websiteId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getId();
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();

    $store->setCode(
        'second_category_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Fixture Store'
    )->setSortOrder(
        10
    )->setIsActive(
        1
    );
    $store->save();
}

/** @var Category $newCategory */
$newCategory = $factory->create();
$newCategory
    ->setName('Category')
    ->setParentId(2)
    ->setLevel(2)
    ->setPath('1/2/3')
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1);
$repository->save($newCategory);
$currentStoreId = $storeManager->getStore()->getId();
$storeManager->setCurrentStore($storeManager->getStore($store->getId()));
$newCategory->setUrlKey('category-3-on-2');
$repository->save($newCategory);
$storeManager->setCurrentStore($storeManager->getStore($currentStoreId));
