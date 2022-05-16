<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/configurable_products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple_10');
$product->setStockData(['use_config_manage_stock' => 1, 'qty' => 1, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);
$productRepository->save($product);

$product = $productRepository->get('simple_20');
$product->setStockData(['use_config_manage_stock' => 1, 'qty' => 0, 'is_qty_decimal' => 0, 'is_in_stock' => 0]);
$productRepository->save($product);

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$request = $objectManager->create(DataObject::class);

/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
/** @var  $attribute */
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');

$request->setData(
    [
        'product_id' => $productRepository->get('configurable')->getId(),
        'selected_configurable_option' => '1',
        'super_attribute' => [
            $attribute->getAttributeId() => $attribute->getOptions()[1]->getValue()
        ],
        'qty' => '1'
    ]
);

$quote->setStoreId(1)
    ->setIsActive(
        true
    )->setIsMultiShipping(
        false
    )->setReservedOrderId(
        'test_order_with_configurable_product'
    )->setEmail(
        'store@example.com'
    )->addProduct(
        $productRepository->get('configurable'),
        $request
    );

/** @var QuoteRepository $quoteRepository */
$quoteRepository = $objectManager->create(
    QuoteRepository::class
);
$quote->collectTotals();
$quoteRepository->save($quote);

/** @var Session $session */
$session = $objectManager->create(
    Session::class
);
$session->setQuoteId($quote->getId());
