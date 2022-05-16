<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\DataObject;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/product_configurable.php');
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */

$objectManager = Bootstrap::getObjectManager();
/** @var $product Product */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('configurable');
/* Create simple products per each option */
/** @var $options Collection */
$options = $objectManager->create(
    Collection::class
);
/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'test_configurable');
$option = $options->setAttributeFilter($attribute->getId())->getFirstItem();

$requestInfo = new DataObject(
    [
        'product' => 1,
        'selected_configurable_option' => 1,
        'qty' => 1,
        'super_attribute' => [
            $attribute->getId() => $option->getId()
        ]
    ]
);

/** @var $cart Cart */
$cart = $objectManager->create(Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->getQuote()->setReservedOrderId('test_cart_with_configurable');
$cart->save();

/** @var $objectManager ObjectManager */
$objectManager->removeSharedInstance(Session::class);

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = Bootstrap::getObjectManager()
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($cart->getQuote()->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
