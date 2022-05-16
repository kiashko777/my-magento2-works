<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManager */

use Magento\Checkout\Model\Cart;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
$objectManager->get(Registry::class)->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get(Registry::class)->unregister('_singleton/Magento_Checkout_Model_Cart');
/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->get(Cart::class);

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
$objectManager->get(Registry::class)->register('product/quoteItemId', $quoteItemId);
$objectManager->get(Registry::class)->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get(Registry::class)->unregister('_singleton/Magento_Checkout_Model_Cart');
