<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Downloadable\Api\Data\LinkInterfaceFactory;
use Magento\Downloadable\Api\DomainManagerInterface;
use Magento\Downloadable\Helper\Download;
use Magento\Downloadable\Model\Link;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var DomainManagerInterface $domainManager */
$domainManager = $objectManager->get(DomainManagerInterface::class);
$domainManager->addDomains(
    [
        'example.com',
        'www.example.com',
        'www.sample.example.com',
        'google.com'
    ]
);

/** @var Product $product */
$product = $objectManager->create(Product::class);
$product
    ->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Downloadable Products')
    ->setSku('downloadable-product')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setLinksPurchasedSeparately(true)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    );

/**
 * @var LinkInterfaceFactory $linkFactory
 */
$linkFactory = Bootstrap::getObjectManager()
    ->get(LinkInterfaceFactory::class);
$links = [];
$linkData = [
    'title' => 'Downloadable Products link',
    'type' => Download::LINK_TYPE_URL,
    'is_shareable' => Link::LINK_SHAREABLE_CONFIG,
    'link_url' => 'http://example.com/downloadable.txt',
    'link_id' => 0,
    'is_delete' => null,
];
$link = $linkFactory->create(['data' => $linkData]);
$link->setId(null);
$link->setLinkType($linkData['type']);
$link->setStoreId($product->getStoreId());
$link->setWebsiteId($product->getStore()->getWebsiteId());
$link->setProductWebsiteIds($product->getWebsiteIds());
$link->setSortOrder(1);
$link->setPrice(0);
$link->setNumberOfDownloads(0);
$links[] = $link;
$extension = $product->getExtensionAttributes();
$extension->setDownloadableProductLinks($links);
$product->setExtensionAttributes($extension);

$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
$productRepository->save($product);
