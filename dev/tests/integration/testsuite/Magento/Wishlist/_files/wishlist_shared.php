<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Wishlist\Model\Wishlist;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$simpleProduct = $productRepository->get('simple');

$options = [];
foreach ($simpleProduct->getOptions() as $option) {
    /* @var $option Option */
    switch ($option->getType()) {
        case 'field':
            $options[$option->getId()] = '1-text';
            break;
        case 'date_time':
            $options[$option->getId()] = ['month' => 1, 'day' => 1, 'year' => 2001, 'hour' => 1, 'minute' => 1];
            break;
        case 'drop_down':
            $options[$option->getId()] = '1';
            break;
        case 'radio':
            $options[$option->getId()] = '1';
            break;
    }
}

/* @var $wishlist Wishlist */
$wishlist = Bootstrap::getObjectManager()->create(
    Wishlist::class
);
$wishlist->loadByCustomerId($customer->getId(), true);
$wishlist->addNewItem($simpleProduct, new DataObject(['options' => $options]));
$wishlist->setSharingCode('fixture_unique_code')
    ->setShared(1)
    ->save();
