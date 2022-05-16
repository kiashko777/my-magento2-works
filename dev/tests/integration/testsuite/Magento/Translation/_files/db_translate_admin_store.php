<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Translation\Model\ResourceModel\StringUtils;

Bootstrap::getObjectManager()->get(
    AreaList::class
)->getArea(
    FrontNameResolver::AREA_CODE
)->load(
    Area::PART_CONFIG
);
/** @var StringUtils $translateString */
$translateString = Bootstrap::getObjectManager()->create(
    StringUtils::class
);
$translateString->saveTranslate('string to translate', 'predefined string translation', null);
