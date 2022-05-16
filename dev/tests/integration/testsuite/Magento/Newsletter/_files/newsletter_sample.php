<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Newsletter\Model\Template;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Template $template */
$template = Bootstrap::getObjectManager()->create(
    Template::class
);

$templateData = [
    'template_code' => 'some_unique_code',
    'template_type' => TemplateTypesInterface::TYPE_TEXT,
    'subject' => 'test data2__22',
    'template_sender_email' => 'sender@email.com',
    'template_sender_name' => 'Test Sender Name 222',
    'text' => 'Template Content 222',
];
$template->setData($templateData);
$template->save();
