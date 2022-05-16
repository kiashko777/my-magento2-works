<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Observer;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateOrderStatusForPaymentMethodsObserverTest extends TestCase
{
    /**
     * @var Observer
     */
    protected $_eventObserver;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Check that \Magento\Payment\Observer\UpdateOrderStatusForPaymentMethodsObserver::execute()
     * is called as event and it can change status
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Payment/_files/order_status.php
     */
    public function testUpdateOrderStatusForPaymentMethodsEvent()
    {
        $statusCode = 'custom_new_status';
        $data = [
            'section' => 'payment',
            'website' => 1,
            'store' => 1,
            'groups' => ['checkmo' => ['fields' => ['order_status' => ['value' => $statusCode]]]],
        ];
        $this->_objectManager->create(
            \Magento\Config\Model\Config::class
        )->setSection(
            'payment'
        )->setWebsite(
            'base'
        )->setGroups(
            ['groups' => $data['groups']]
        )->save();

        /** @var Status $status */
        $status = $this->_objectManager->get(Status::class)->load($statusCode);

        /** @var $scopeConfig ScopeConfigInterface */
        $scopeConfig = $this->_objectManager->get(ScopeConfigInterface::class);
        $defaultStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            ScopeInterface::SCOPE_STORE
        );

        /** @var Config $config */
        $config = $this->_objectManager->get(Config::class);
        $config->saveConfig(
            'payment/checkmo/order_status',
            $statusCode,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->_resetConfig();

        $newStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            ScopeInterface::SCOPE_STORE
        );

        $status->unassignState(Order::STATE_NEW);

        $this->_resetConfig();

        $unassignedStatus = $scopeConfig->getValue(
            'payment/checkmo/order_status',
            ScopeInterface::SCOPE_STORE
        );

        $this->assertEquals('pending', $defaultStatus);
        $this->assertEquals($statusCode, $newStatus);
        $this->assertEquals('pending', $unassignedStatus);
    }

    /**
     * Clear config cache
     */
    protected function _resetConfig()
    {
        $this->_objectManager->get(ReinitableConfigInterface::class)->reinit();
        $this->_objectManager->create(StoreManagerInterface::class)->reinitStores();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testUpdateOrderStatusForPaymentMethods()
    {
        $statusCode = 'custom_new_status';

        /** @var Config $config */
        $config = $this->_objectManager->get(Config::class);
        $config->saveConfig('payment/checkmo/order_status', $statusCode, 'default', 0);

        $this->_resetConfig();

        $observer = $this->_objectManager->create(
            UpdateOrderStatusForPaymentMethodsObserver::class
        );
        $observer->execute($this->_eventObserver);

        $this->_resetConfig();

        /** @var $scopeConfig ScopeConfigInterface */
        $scopeConfig = $this->_objectManager->get(ScopeConfigInterface::class);
        $unassignedStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            ScopeInterface::SCOPE_STORE
        );
        $this->assertEquals('pending', $unassignedStatus);
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_eventObserver = $this->_createEventObserver();
    }

    /**
     * Create event observer
     *
     * @return Observer
     */
    protected function _createEventObserver()
    {
        $data = ['status' => 'custom_new_status', 'state' => Order::STATE_NEW];
        $event = $this->_objectManager->create(Event::class, ['data' => $data]);
        return $this->_objectManager
            ->create(Observer::class, ['data' => ['event' => $event]]);
    }
}
