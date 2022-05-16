<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;

$objectManager = Bootstrap::getObjectManager();

/** @var Theme $theme */
$theme = $objectManager->create(Theme::class);
$theme->load('Magento/zoom1', 'theme_path');
$theme->delete();

$theme = $objectManager->create(Theme::class);
$theme->load('Magento/zoom2', 'theme_path');
$theme->delete();

$theme = $objectManager->create(Theme::class);
$theme->load('Magento/zoom3', 'theme_path');
$theme->delete();
