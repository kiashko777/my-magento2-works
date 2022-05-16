<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var StringUtils $translateString */

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Translation\Model\ResourceModel\StringUtils;

$translateString = Bootstrap::getObjectManager()->create(
    StringUtils::class
);
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
