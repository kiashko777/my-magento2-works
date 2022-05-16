<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Magento\Cms\Api\PageRepositoryInterface $pageRepository */
$pageRepository = Bootstrap::getObjectManager()->get(
    Magento\Cms\Api\PageRepositoryInterface::class
);

$pageRepository->deleteById('page-a');
$pageRepository->deleteById('page-b');
$pageRepository->deleteById('page-c');
$pageRepository->deleteById('page-d');
$pageRepository->deleteById('page-e');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);

/** @var UrlRewriteCollection $urlRewriteCollection */
$urlRewriteCollection = Bootstrap::getObjectManager()
    ->create(UrlRewriteCollection::class);
$collection = $urlRewriteCollection
    ->addFieldToFilter('entity_type', 'custom')
    ->addFieldToFilter(
        'target_path',
        [
            'page-a/',
            'page-a',
            'page-b',
            'page-c',
            'page-d?param1=1',
            'page-e?param1=1',
            'http://example.com/external',
            'https://example.com/external2/',
            'http://example.com/external?param1=value1',
            'https://example.com/external2/?param2=value2',
            '/',
            'contact?param1=1'
        ]
    )
    ->load()
    ->walk('delete');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
