<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\UrlRewrite;

$objectManager = Bootstrap::getObjectManager();

$rewritesData = [
    [
        'string', 'test_page1', 0
    ],
    [
        'string_permanent', 'test_page1', OptionProvider::PERMANENT
    ],
    [
        'string_temporary', 'test_page1', OptionProvider::TEMPORARY
    ],
    [
        'строка', 'test_page1', 0
    ],
    [
        urlencode('строка'), 'test_page2', 0
    ],
    [
        'другая_строка', 'test_page1', OptionProvider::TEMPORARY
    ],
    [
        'السلسلة', 'test_page1', 0
    ],
];

$rewriteResource = $objectManager->create(\Magento\UrlRewrite\Model\ResourceModel\UrlRewrite::class);
foreach ($rewritesData as $rewriteData) {
    list ($requestPath, $targetPath, $redirectType) = $rewriteData;
    $rewrite = $objectManager->create(UrlRewrite::class);
    $rewrite->setEntityType('custom')
        ->setRequestPath($requestPath)
        ->setTargetPath($targetPath)
        ->setRedirectType($redirectType);
    $rewriteResource->save($rewrite);
}
