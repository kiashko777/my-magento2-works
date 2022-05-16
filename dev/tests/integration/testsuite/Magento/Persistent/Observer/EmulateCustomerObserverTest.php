<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Persistent\Helper\Session;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 */
class EmulateCustomerObserverTest extends TestCase
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var EmulateCustomerObserver
     */
    protected $_observer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     * @magentoConfigFixture current_store persistent/options/enabled 1
     */
    public function testEmulateCustomer()
    {
        $observer = new Observer();

        $this->_customerSession->loginById(1);
        $this->_customerSession->logout();
        $this->assertNull($this->_customerSession->getCustomerId());
        $this->assertEquals(
            GroupManagement::NOT_LOGGED_IN_ID,
            $this->_customerSession->getCustomerGroupId()
        );

        $this->_observer->execute($observer);
        $customer = $this->customerRepository->getById(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->assertEquals(
            $customer->getId(),
            $this->_customerSession->getCustomerId()
        );
        $this->assertEquals(
            $customer->getGroupId(),
            $this->_customerSession->getCustomerGroupId()
        );
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();

        $this->_customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);

        $this->customerRepository = $this->_objectManager->create(
            CustomerRepositoryInterface::class
        );
        $this->_persistentSessionHelper = $this->_objectManager->create(Session::class);

        $this->_observer = $this->_objectManager->create(
            EmulateCustomerObserver::class,
            [
                'customerRepository' => $this->customerRepository,
                'persistentSession' => $this->_persistentSessionHelper
            ]
        );
    }
}
