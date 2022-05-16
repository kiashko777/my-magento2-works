<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Captcha\Observer;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test captcha observer behavior.
 *
 * @magentoAppArea Adminhtml
 */
class CaseCaptchaIsRequiredAfterFailedLoginAttemptsTest extends AbstractController
{
    /**
     * @magentoAdminConfigFixture admin/captcha/forms backend_login
     * @magentoAdminConfigFixture admin/captcha/enable 1
     * @magentoAdminConfigFixture admin/captcha/mode always
     */
    public function testBackendLoginActionWithInvalidCaptchaReturnsError()
    {
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOffSecretKey();

        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $post = [
            'login' => [
                'username' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
                'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
            ],
            'captcha' => ['backend_login' => 'some_unrealistic_captcha_value'],
            'form_key' => $formKey->getFormKey(),
        ];
        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/admin');
        $this->assertSessionMessages($this->equalTo([(string)__('Incorrect CAPTCHA.')]), MessageInterface::TYPE_ERROR);
        $backendUrlModel = Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $backendUrlModel->turnOffSecretKey();
        $url = $backendUrlModel->getUrl('admin');
        $this->assertRedirect($this->stringStartsWith($url));
    }
}
