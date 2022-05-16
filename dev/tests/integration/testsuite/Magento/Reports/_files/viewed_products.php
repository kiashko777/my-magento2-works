<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Reports\Model\ResourceModel\Report\Product\Viewed;
use Magento\Reports\Observer\CatalogProductViewObserver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getObjectManager()->get(AreaList::class)
    ->getArea('Adminhtml')
    ->load(Area::PART_CONFIG);
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_duplicated.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual.php');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);

$simpleId = $productRepository->get('simple')->getId();
$simpleDuplicatedId = $productRepository->get('simple-1')->getId();
$virtualId = $productRepository->get('virtual-product')->getId();

$config = Bootstrap::getObjectManager()->get(
    MutableScopeConfigInterface::class
);
$config->setValue(
    'reports/options/enabled',
    1,
    ScopeConfigInterface::SCOPE_TYPE_DEFAULT
);

// imitate product views
/** @var CatalogProductViewObserver $reportObserver */
$reportObserver = Bootstrap::getObjectManager()->create(
    CatalogProductViewObserver::class
);

$productIds = [$simpleId, $simpleDuplicatedId, $simpleId, $virtualId, $simpleId, $virtualId];

foreach ($productIds as $productId) {
    $reportObserver->execute(
        new Observer(
            [
                'event' => new DataObject(
                    [
                        'product' => new DataObject(['id' => $productId]),
                    ]
                ),
            ]
        )
    );
}

// refresh report statistics
/** @var Viewed $reportResource */
$reportResource = Bootstrap::getObjectManager()->create(
    Viewed::class
);
$reportResource->beginTransaction();
// prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
