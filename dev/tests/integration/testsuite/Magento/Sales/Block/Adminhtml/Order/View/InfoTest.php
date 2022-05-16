<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\TestCase\AbstractBackendController;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\View\Info
 */
class InfoTest extends AbstractBackendController
{
    /**
     * Value for the user defined custom attribute, which is created by attribute_user_defined_customer.php fixture.
     */
    const ORDER_USER_DEFINED_ATTRIBUTE_VALUE = 'custom_attr_value';

    public function testCustomerGridAction()
    {
        $layout = $this->_objectManager->get(LayoutInterface::class);
        /** @var Info $infoBlock */
        $infoBlock = $layout->createBlock(
            Info::class,
            'info_block' . random_int(0, PHP_INT_MAX),
            []
        );

        $result = $infoBlock->getCustomerAccountData();
        $this->assertEquals([], $result, 'Customer has additional account data.');
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testGetCustomerGroupName()
    {
        $layout = $this->_objectManager->get(LayoutInterface::class);
        /** @var Info $customerGroupBlock */
        $customerGroupBlock = $layout->createBlock(
            Info::class,
            'info_block' . random_int(0, PHP_INT_MAX),
            ['registry' => $this->_putOrderIntoRegistry()]
        );

        $result = $customerGroupBlock->getCustomerGroupName();
        $this->assertEquals('NOT LOGGED IN', $result);
    }

    /**
     * @param array $additionalOrderData
     * @return Registry|MockObject
     */
    protected function _putOrderIntoRegistry(array $additionalOrderData = [])
    {
        $registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();

        $order = $this->_objectManager->get(
            Order::class
        )->load(
            '100000001'
        )->setData(
            array_merge(['customer_group_id' => 0], $additionalOrderData)
        );

        $registry->expects($this->any())->method('registry')->with('current_order')->willReturn($order);

        return $registry;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public function testGetCustomerAccountData()
    {
        $layout = $this->_objectManager->get(LayoutInterface::class);

        $orderData = [
            'customer_' . FIXTURE_ATTRIBUTE_USER_DEFINED_CUSTOMER_NAME => self::ORDER_USER_DEFINED_ATTRIBUTE_VALUE,
        ];
        /** @var Info $customerGroupBlock */
        $customerGroupBlock = $layout->createBlock(
            Info::class,
            'info_block' . random_int(0, PHP_INT_MAX),
            ['registry' => $this->_putOrderIntoRegistry($orderData)]
        );

        $this->assertEquals(
            [
                200 => [
                    'label' => FIXTURE_ATTRIBUTE_USER_DEFINED_CUSTOMER_FRONTEND_LABEL,
                    'value' => self::ORDER_USER_DEFINED_ATTRIBUTE_VALUE,
                ],
            ],
            $customerGroupBlock->getCustomerAccountData()
        );
    }
}
