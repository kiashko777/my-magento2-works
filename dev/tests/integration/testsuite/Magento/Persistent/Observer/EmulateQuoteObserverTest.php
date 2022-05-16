<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Persistent\Helper\Session;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 */
class EmulateQuoteObserverTest extends TestCase
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
     * @var EmulateQuoteObserver
     */
    protected $_observer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session | MockObject
     */
    protected $_checkoutSession;

    /**
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     */
    public function testEmulateQuote()
    {
        $requestMock = $this->getMockBuilder(
            Http::class
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $requestMock->expects($this->once())->method('getFullActionName')->willReturn('valid_action');
        $event = new Event(['request' => $requestMock]);
        $observer = new Observer();
        $observer->setEvent($event);

        $this->_customerSession->loginById(1);

        $customer = $this->customerRepository->getById(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->_checkoutSession->expects($this->once())->method('setCustomerData')->with($customer);
        $this->_customerSession->logout();

        $this->_observer->execute($observer);
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();

        $this->_customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);

        $this->customerRepository = $this->_objectManager->create(
            CustomerRepositoryInterface::class
        );

        $this->_checkoutSession = $this->getMockBuilder(
            \Magento\Checkout\Model\Session::class
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $this->_persistentSessionHelper = $this->_objectManager->create(Session::class);

        $this->_observer = $this->_objectManager->create(
            EmulateQuoteObserver::class,
            [
                'customerRepository' => $this->customerRepository,
                'checkoutSession' => $this->_checkoutSession,
                'persistentSession' => $this->_persistentSessionHelper
            ]
        );
    }
}
