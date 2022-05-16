<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManagerInterface */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Widget\Model\Layout\Update;

$objectManager = Bootstrap::getObjectManager();
$objectManager->get(AreaList::class)
    ->getArea(FrontNameResolver::AREA_CODE)
    ->load(Area::PART_CONFIG);
/** @var $theme ThemeInterface */
$theme = $objectManager->create(ThemeInterface::class);
$theme->setThemePath(
    'test/test'
)->setArea(
    'frontend'
)->setThemeTitle(
    'Test Theme'
)->setType(
    ThemeInterface::TYPE_VIRTUAL
)->save();

/** @var $updateNotTemporary Update */
$updateNotTemporary = $objectManager->create(Update::class);
$updateNotTemporary->setHandle(
    'test_handle'
)->setXml(
    'not_temporary'
)->setStoreId(
    0
)->setThemeId(
    $theme->getId()
)->save();

/** @var $updateTemporary Update */
$updateTemporary = $objectManager->create(Update::class);
$updateTemporary->setHandle(
    'test_handle'
)->setIsTemporary(
    1
)->setXml(
    'temporary'
)->setStoreId(
    0
)->setThemeId(
    $theme->getId()
)->save();
