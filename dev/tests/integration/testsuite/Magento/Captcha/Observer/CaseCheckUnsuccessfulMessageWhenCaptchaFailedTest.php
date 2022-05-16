<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Captcha\Observer;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test captcha observer behavior
 *
 * @magentoAppArea Adminhtml
 */
class CaseCheckUnsuccessfulMessageWhenCaptchaFailedTest extends AbstractController
{
    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAdminConfigFixture admin/captcha/enable 1
     * @magentoAdminConfigFixture admin/captcha/forms backend_forgotpassword
     * @magentoAdminConfigFixture admin/captcha/mode always
     */
    public function testCheckUnsuccessfulMessageWhenCaptchaFailed()
    {
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOffSecretKey();
        $this->getRequest()->setPostValue(['email' => 'dummy@dummy.com', 'captcha' => '1234']);
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertSessionMessages(
            $this->equalTo(['Incorrect CAPTCHA']),
            MessageInterface::TYPE_ERROR
        );
    }
}
