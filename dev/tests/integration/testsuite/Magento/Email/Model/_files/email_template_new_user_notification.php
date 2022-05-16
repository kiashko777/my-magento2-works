<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Email\Model\Template;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Template $template */
$template = $objectManager->create(Template::class);
$template->setOptions(['area' => 'test area', 'store' => 1]);
$templateText = '{{trans "New User Notification Custom Text %first_name, ' .
    '%last_name" first_name=$user.firstname last_name=$user.lastname}}';
$template->setData(
    [
        'template_text' => $templateText,
        'template_code' => 'New User Notification Custom Code',
        'template_type' => Template::TYPE_TEXT,
        'orig_template_code' => 'admin_emails_new_user_notification_template'
    ]
);
$template->save();
