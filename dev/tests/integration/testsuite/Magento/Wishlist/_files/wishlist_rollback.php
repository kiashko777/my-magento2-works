<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Wishlist\Model\Wishlist;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Wishlist $wishlist */
$wishlist = $objectManager->create(Wishlist::class);
$wishlist->loadByCustomerId(1);
$wishlist->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
