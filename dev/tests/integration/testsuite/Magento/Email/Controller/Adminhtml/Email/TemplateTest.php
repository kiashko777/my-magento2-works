<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Email\Controller\Adminhtml\Email;

use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class TemplateTest extends AbstractBackendController
{
    public function testDefaultTemplateAction()
    {
        /** @var $formKey FormKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $post = [
            'form_key' => $formKey->getFormKey(),
            'code' => 'customer_password_forgot_email_template',
        ];
        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/admin/email_template/defaultTemplate/?isAjax=true');
        $this->assertStringContainsString(
            '"template_type":2,"template_subject":"{{trans \"Reset your',
            $this->getResponse()->getBody()
        );
    }
}
