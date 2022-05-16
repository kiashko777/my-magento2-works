<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\DataObject;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
/** @var QuoteFactory $quoteFactory */
$quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = Bootstrap::getObjectManager()->get(QuoteResource::class);
/** @var CartRepositoryInterface $cartRepository */
$cartRepository = Bootstrap::getObjectManager()->get(CartRepositoryInterface::class);

/** @var $product Product */
$product = $productRepository->get('configurable');
/** @var $options Collection */
$options = Bootstrap::getObjectManager()->create(
    Collection::class
);

$attributeRepository = Bootstrap::getObjectManager()->get(AttributeRepositoryInterface::class);
$attribute = $attributeRepository->get('catalog_product', 'test_configurable');

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


$quote = $quoteFactory->create();
$quoteResource->load($quote, 'test_quote', 'reserved_order_id');
$quote->addProduct($product, $requestInfo);
$cartRepository->save($quote);
