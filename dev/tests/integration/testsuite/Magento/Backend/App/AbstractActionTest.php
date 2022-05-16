<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\App;

use Magento\AdminNotification\Model\Inbox;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Acl;
use Magento\Framework\Acl\Builder;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test class for \Magento\Backend\App\AbstractAction.
 * @magentoAppArea Adminhtml
 */
class AbstractActionTest extends AbstractBackendController
{
    /**
     * Check redirection to startup page for logged user
     * @magentoConfigFixture current_store admin/security/use_form_key 1
     * @magentoAppIsolation enabled
     */
    public function testPreDispatchWithEmptyUrlRedirectsToStartupPage()
    {
        $this->markTestSkipped('Session destruction doesn\'t work');
        Bootstrap::getObjectManager()->get(
            ScopeInterface::class
        )->setCurrentScope(
            FrontNameResolver::AREA_CODE
        );
        $this->dispatch('backend');
        /** @var $backendUrlModel UrlInterface */
        $backendUrlModel = Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $url = $backendUrlModel->getStartupPageUrl();
        $expected = $backendUrlModel->getUrl($url);
        $this->assertRedirect($this->stringStartsWith($expected));
    }

    /**
     * Check login redirection
     *
     * @magentoDbIsolation enabled
     */
    public function testInitAuthentication()
    {
        /**
         * Logout current session
         */
        $this->_auth->logout();

        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $postLogin = [
            'login' => [
                'username' => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
                'password' => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
            ],
            'form_key' => $formKey->getFormKey(),
        ];

        $this->getRequest()->setPostValue($postLogin);
        $this->dispatch('backend/admin/system_account/index');

        $expected = 'backend/admin/system_account/index';
        $this->assertRedirect($this->stringContains($expected));
    }

    /**
     * Check layout attribute "acl" for check access to
     *
     * @param string $blockName
     * @param string $resource
     * @param bool $isLimitedAccess
     * @dataProvider nodesWithAcl
     */
    public function testAclInNodes($blockName, $resource, $isLimitedAccess)
    {
        /** @var $noticeInbox Inbox */
        $noticeInbox = Bootstrap::getObjectManager()->create(
            Inbox::class
        );
        if (!$noticeInbox->loadLatestNotice()->getId()) {
            $noticeInbox->addCritical('Test notice', 'Test description');
        }

        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );

        /** @var $acl Acl */
        $acl = Bootstrap::getObjectManager()
            ->get(Builder::class)
            ->getAcl();
        if ($isLimitedAccess) {
            $acl->deny(null, $resource);
        }

        $this->dispatch('backend/admin/dashboard');

        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $actualBlocks = $layout->getAllBlocks();

        $this->assertNotEmpty($actualBlocks);
        if ($isLimitedAccess) {
            $this->assertNotContains($blockName, array_keys($actualBlocks));
        } else {
            $this->assertContains($blockName, array_keys($actualBlocks));
        }
    }

    /**
     * Data provider with expected blocks with acl properties
     *
     * @return array
     */
    public function nodesWithAcl()
    {
        return [
            ['notification_window', 'Magento_AdminNotification::show_toolbar', true],
            ['notification_window', 'Magento_AdminNotification::show_toolbar', false]
        ];
    }
}
