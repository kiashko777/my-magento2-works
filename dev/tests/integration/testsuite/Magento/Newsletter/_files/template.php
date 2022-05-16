<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Newsletter\Model\Template;
use Magento\TestFramework\Helper\Bootstrap;

$template = Bootstrap::getObjectManager()->create(
    Template::class
);
$template->setTemplateCode(
    'fixture_tpl'
)->setTemplateText(
    '<p>Follow this link to unsubscribe</p>
<!-- This tag is for unsubscribe link  -->
<p><a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}</a></p>'
)->setTemplateType(
    2
)->setTemplateSubject(
    'Subject'
)->setTemplateSenderName(
    'CustomerSupport'
)->setTemplateSenderEmail(
    'support@example.com'
)->setTemplateActual(
    1
)->save();
