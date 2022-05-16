<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Email\Model\Template;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Config\ValidatorTest;

$objectManager = Bootstrap::getObjectManager();
/** @var Template $template */
$template = $objectManager->create(Template::class);
$template->setOptions(['area' => 'test area', 'store' => 1]);
$template->setData(
    [
        'template_text' => file_get_contents(__DIR__ . '/template_fixture.html'),
        'template_code' => ValidatorTest::TEMPLATE_CODE,
        'template_type' => Template::TYPE_TEXT
    ]
);
$template->save();
