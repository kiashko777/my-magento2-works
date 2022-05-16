<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Downloadable\Api\Data\LinkInterfaceFactory;
use Magento\Downloadable\Api\Data\SampleInterfaceFactory;
use Magento\Downloadable\Api\DomainManagerInterface;
use Magento\Downloadable\Helper\Download;
use Magento\Downloadable\Model\Link;
use Magento\Downloadable\Model\Product\Type as ProductType;
use Magento\TestFramework\Helper\Bootstrap;

/** @var DomainManagerInterface $domainManager */
$domainManager = Bootstrap::getObjectManager()->get(DomainManagerInterface::class);
$domainManager->addDomains(['example.com']);

/**
 * @var Product $product
 */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product
    ->setTypeId(ProductType::TYPE_DOWNLOADABLE)
    ->setAttributeSetId(4)
    ->setStoreId(1)
    ->setWebsiteIds([1])
    ->setName('Downloadable Products (Links can be purchased separately)')
    ->setSku('downloadable-product-with-purchased-separately-links')
    ->setPrice(10)
    ->setVisibility(ProductVisibility::VISIBILITY_BOTH)
    ->setStatus(ProductStatus::STATUS_ENABLED)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    );

/**
 * @var LinkInterfaceFactory $linkFactory1
 */
$linkFactory1 = Bootstrap::getObjectManager()
    ->get(LinkInterfaceFactory::class);
$link1 = $linkFactory1->create();
$link1
    ->setTitle('Downloadable Products link 1')
    ->setLinkType(Download::LINK_TYPE_URL)
    ->setIsShareable(Link::LINK_SHAREABLE_CONFIG)
    ->setLinkUrl('http://example.com/downloadable1.txt')
    ->setStoreId($product->getStoreId())
    ->setWebsiteId($product->getStore()->getWebsiteId())
    ->setProductWebsiteIds($product->getWebsiteIds())
    ->setSortOrder(10)
    ->setPrice(2.0000)
    ->setNumberOfDownloads(0);
/**
 * @var LinkInterfaceFactory $linkFactory2
 */
$linkFactory2 = Bootstrap::getObjectManager()
    ->get(LinkInterfaceFactory::class);
$link2 = $linkFactory2->create();
$link2
    ->setTitle('Downloadable Products link 2')
    ->setLinkType(Download::LINK_TYPE_URL)
    ->setIsShareable(Link::LINK_SHAREABLE_CONFIG)
    ->setLinkUrl('http://example.com/downloadable2.txt')
    ->setStoreId($product->getStoreId())
    ->setWebsiteId($product->getStore()->getWebsiteId())
    ->setProductWebsiteIds($product->getWebsiteIds())
    ->setSortOrder(20)
    ->setPrice(4.0000)
    ->setNumberOfDownloads(0);

/**
 * @var SampleInterfaceFactory $sampleFactory
 */
$sampleFactory = Bootstrap::getObjectManager()
    ->get(SampleInterfaceFactory::class);
$sample1 = $sampleFactory->create();
$sample1->setTitle('Downloadable Products Sample')
    ->setSampleType(Download::LINK_TYPE_URL)
    ->setSampleUrl('http://example.com/downloadable.txt')
    ->setStoreId($product->getStoreId())
    ->setWebsiteId($product->getStore()->getWebsiteId())
    ->setProductWebsiteIds($product->getWebsiteIds())
    ->setSortOrder(10);

$extension = $product->getExtensionAttributes();
$extension->setDownloadableProductLinks([$link1, $link2]);
$extension->setDownloadableProductSamples([$sample1]);

$product->setExtensionAttributes($extension);
$product->setLinksPurchasedSeparately(true);

$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
$productRepository->save($product);
