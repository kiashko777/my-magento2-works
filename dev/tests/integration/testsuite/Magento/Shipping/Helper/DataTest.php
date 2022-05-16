<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Shipping\Helper;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param string $modelName
     * @param string $getIdMethod
     * @param int $entityId
     * @param string $code
     * @param string $expected
     * @dataProvider getTrackingPopupUrlBySalesModelDataProvider
     */
    public function testGetTrackingPopupUrlBySalesModel($modelName, $getIdMethod, $entityId, $code, $expected)
    {
        $objectManager = Bootstrap::getObjectManager();
        $constructArgs = [];
        if (Shipment::class === $modelName) {
            $orderRepository = $this->getMockOrderRepository($code);
            $constructArgs['orderRepository'] = $orderRepository;
        } elseif (Track::class === $modelName) {
            $shipmentRepository = $this->getMockShipmentRepository($code);
            $constructArgs['shipmentRepository'] = $shipmentRepository;
        }

        $model = $objectManager->create($modelName, $constructArgs);
        $model->{$getIdMethod}($entityId);

        if (Order::class === $modelName) {
            $model->setProtectCode($code);
        }
        if (Track::class === $modelName) {
            $model->setParentId(1);
        }

        $actual = $this->helper->getTrackingPopupUrlBySalesModel($model);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param $code
     * @return OrderRepositoryInterface|MockObject
     */
    private function getMockOrderRepository($code)
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->create(Order::class);
        $order->setProtectCode($code);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->expects($this->atLeastOnce())->method('get')->willReturn($order);
        return $orderRepository;
    }

    /**
     * @param $code
     * @return ShipmentRepository|MockObject
     */
    private function getMockShipmentRepository($code)
    {
        $objectManager = Bootstrap::getObjectManager();
        $orderRepository = $this->getMockOrderRepository($code);
        $shipmentArgs = ['orderRepository' => $orderRepository];

        $shipment = $objectManager->create(Shipment::class, $shipmentArgs);
        $shipmentRepository = $this->createPartialMock(ShipmentRepository::class, ['get']);
        $shipmentRepository->expects($this->atLeastOnce())->method('get')->willReturn($shipment);
        return $shipmentRepository;
    }

    /**
     * From the admin panel with custom URL we should have generated frontend URL
     *
     * @param string $modelName
     * @param string $getIdMethod
     * @param int $entityId
     * @param string $code
     * @param string $expected
     * @magentoAppArea Adminhtml
     * @magentoConfigFixture admin_store web/unsecure/base_link_url http://admin.localhost/
     * @dataProvider getTrackingPopupUrlBySalesModelDataProvider
     */
    public function testGetTrackingPopupUrlBySalesModelFromAdmin($modelName, $getIdMethod, $entityId, $code, $expected)
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var StoreManagerInterface $storeManager */
        $storeManager = $objectManager->create(StoreManagerInterface::class);
        $storeManager->reinitStores();

        $constructArgs = [];
        if (Shipment::class === $modelName) {
            $orderRepository = $this->getMockOrderRepository($code);
            $constructArgs['orderRepository'] = $orderRepository;
        } elseif (Track::class === $modelName) {
            $shipmentRepository = $this->getMockShipmentRepository($code);
            $constructArgs['shipmentRepository'] = $shipmentRepository;
        }

        $model = $objectManager->create($modelName, $constructArgs);
        $model->{$getIdMethod}($entityId);

        if (Order::class === $modelName) {
            $model->setProtectCode($code);
        }
        if (Track::class === $modelName) {
            $model->setParentId(1);
        }

        //Frontend URL should be used there
        $actual = $this->helper->getTrackingPopupUrlBySalesModel($model);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getTrackingPopupUrlBySalesModelDataProvider()
    {
        return [
            [Order::class,
                'setId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup?hash=b3JkZXJfaWQ6NDI6YWJj',
            ],
            [Shipment::class,
                'setId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup?hash=c2hpcF9pZDo0MjphYmM%2C'
            ],
            [Track::class,
                'setEntityId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup?hash=dHJhY2tfaWQ6NDI6YWJj'
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}
