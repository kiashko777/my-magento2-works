<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class VisitorTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testBindCustomerLogin()
    {
        /** @var Visitor $visitor */
        $visitor = Bootstrap::getObjectManager()->get(Visitor::class);
        $visitor->unsCustomerId();
        $visitor->unsDoCustomerLogin();

        $customer = $this->_loginCustomer('customer@example.com', 'password');

        // Visitor has not customer ID yet
        $this->assertTrue($visitor->getDoCustomerLogin());
        $this->assertEquals($customer->getId(), $visitor->getCustomerId());

        // Visitor already has customer ID
        $visitor->unsDoCustomerLogin();
        $this->_loginCustomer('customer@example.com', 'password');
        $this->assertNull($visitor->getDoCustomerLogin());
    }

    /**
     * Authenticate customer and return its DTO
     * @param string $username
     * @param string $password
     * @return CustomerInterface
     */
    protected function _loginCustomer($username, $password)
    {
        /** @var AccountManagementInterface $accountManagement */
        $accountManagement = Bootstrap::getObjectManager()->create(
            AccountManagementInterface::class
        );
        return $accountManagement->authenticate($username, $password);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testBindCustomerLogout()
    {
        /** @var Visitor $visitor */
        $visitor = Bootstrap::getObjectManager()->get(Visitor::class);

        $this->_loginCustomer('customer@example.com', 'password');
        $visitor->setCustomerId(1);
        $visitor->unsDoCustomerLogout();
        $this->_logoutCustomer(1);

        // Visitor has customer ID => check that do_customer_logout flag is set
        $this->assertTrue($visitor->getDoCustomerLogout());

        $this->_loginCustomer('customer@example.com', 'password');
        $visitor->unsCustomerId();
        $visitor->unsDoCustomerLogout();
        $this->_logoutCustomer(1);

        // Visitor has no customer ID => check that do_customer_logout flag not changed
        $this->assertNull($visitor->getDoCustomerLogout());
    }

    /**
     * Log out customer
     * @param int $customerId
     */
    public function _logoutCustomer($customerId)
    {
        /** @var Session $customerSession */
        $customerSession = Bootstrap::getObjectManager()->get(Session::class);
        $customerSession->setCustomerId($customerId);
        $customerSession->logout();
    }

    /**
     * @magentoAppArea frontend
     */
    public function testClean()
    {
        $customerIdNow = 1;
        $lastVisitNow = date('Y-m-d H:i:s', time());
        $sessionIdNow = 'asaswljxvgklasdflkjasieasd';
        $customerIdPast = null;
        $lastVisitPast = date('Y-m-d H:i:s', time() - 172800);
        $sessionIdPast = 'kui0aa57nqddl8vk7k6ohgi352';

        /** @var Visitor $visitor */
        $visitor = Bootstrap::getObjectManager()->get(Visitor::class);
        $visitor->setCustomerId($customerIdPast);
        $visitor->setSessionId($sessionIdPast);
        $visitor->setLastVisitAt($lastVisitPast);
        $visitor->save();
        $visitorIdPast = $visitor->getId();
        $visitor->unsetData();
        $visitor->setCustomerId($customerIdNow);
        $visitor->setSessionId($sessionIdNow);
        $visitor->setLastVisitAt($lastVisitNow);
        $visitor->save();
        $visitorIdNow = $visitor->getId();
        $visitor->unsetData();

        $visitor->clean();
        $visitor->load($visitorIdPast);
        $this->assertEquals([], $visitor->getData());
        $visitor->unsetData();
        $visitor->load($visitorIdNow);
        $this->assertEquals(
            [
                'visitor_id' => $visitorIdNow,
                'customer_id' => $customerIdNow,
                'session_id' => $sessionIdNow,
                'last_visit_at' => $lastVisitNow
            ],
            $visitor->getData()
        );
    }
}
