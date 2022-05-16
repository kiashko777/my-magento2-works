<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Products generation to test base data
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->loadArea('Adminhtml');

$testCases = include __DIR__ . '/_algorithm_base_data.php';

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);
/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    3
)->setName(
    'Root Category'
)->setParentId(
    2
)->setPath(
    '1/2/3'
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->save();

$lastProductId = 0;
foreach ($testCases as $index => $testCase) {

    /** @var ProductRepositoryInterface $productRepository */
    $productRepository = Bootstrap::getObjectManager()->create(
        ProductRepositoryInterface::class
    );

    foreach ($testCase[0] as $price) {
        /** @var Product $product */
        $product = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $productId = $lastProductId + 1;
        try {
            $product = $productRepository->get('simple-' . $productId, false, null, true);
            $productRepository->delete($product);
        } catch (NoSuchEntityException $e) {
            //Products already removed
        }
        ++$lastProductId;
    }
}

/** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
$collection = Bootstrap::getObjectManager()
    ->create(Collection::class);
$collection
    ->addAttributeToFilter('level', ['in' => [2, 3, 4]])
    ->load()
    ->delete();
