<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class AjaxLoginTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testLogoutAction()
    {
        $this->login(1);
        $this->dispatch('customer/ajax/logout');
        $body = $this->getResponse()->getBody();
        $logoutMessage = Bootstrap::getObjectManager()->get(
            Data::class
        )->jsonDecode($body);
        $this->assertStringContainsString('Logout Successful', $logoutMessage['message']);
    }

    /**
     * Login the user
     *
     * @param string $customerId Customer to mark as logged in for the session
     * @return void
     */
    protected function login($customerId)
    {
        /** @var Session $session */
        $session = Bootstrap::getObjectManager()
            ->get(Session::class);
        $session->loginById($customerId);
    }
}
