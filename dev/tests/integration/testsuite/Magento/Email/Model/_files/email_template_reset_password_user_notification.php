<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Email\Model\Template;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$origTemplateCode = 'admin_emails_forgot_email_template';
/** @var Template $template */
$template = $objectManager->create(Template::class);
$template->loadDefault($origTemplateCode);
$template->setTemplateCode('Reset Password User Notification Custom Code');
$template->setOrigTemplateCode('admin_emails_forgot_email_template');
$template->setId(null);
$template->save();
