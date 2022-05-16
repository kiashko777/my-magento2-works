<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Persistent\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Context;
use Magento\Framework\Escaper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Block\Reorder\Sidebar;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObserverTest extends TestCase
{
    /**
     * @var View
     */
    protected $_customerViewHelper;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Checkout\Model\Session | MockObject
     */
    protected $_checkoutSession;

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     */
    public function testEmulateWelcomeBlock()
    {
        $httpContext = new \Magento\Framework\App\Http\Context();
        $httpContext->setValue(Context::CONTEXT_AUTH, 1, 1);
        $block = $this->_objectManager->create(
            Sidebar::class,
            [
                'httpContext' => $httpContext
            ]
        );
        $this->_observer->emulateWelcomeBlock($block);

        $this->assertEquals('&nbsp;', $block->getWelcome());
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();

        $this->_customerViewHelper = $this->_objectManager->create(
            View::class
        );
        $this->_escaper = $this->_objectManager->create(
            Escaper::class
        );

        $this->customerRepository = $this->_objectManager->create(
            CustomerRepositoryInterface::class
        );

        $this->_checkoutSession = $this->getMockBuilder(
            \Magento\Checkout\Model\Session::class
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $this->_observer = $this->_objectManager->create(
            Observer::class,
            [
                'escaper' => $this->_escaper,
                'customerViewHelper' => $this->_customerViewHelper,
                'customerRepository' => $this->customerRepository,
                'checkoutSession' => $this->_checkoutSession
            ]
        );
    }
}
