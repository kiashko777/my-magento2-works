<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class OrderItemGetTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders/items';

    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'salesOrderItemRepositoryV1';

    const ORDER_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function testGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);
        /** @var Item $orderItem */
        $orderItem = current($order->getItems());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $orderItem->getId(),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, ['id' => $orderItem->getId()]);

        $this->assertIsArray($response);
        $this->assertOrderItem($orderItem, $response);

        //check that nullable fields were marked as optional and were not sent
        foreach ($response as $fieldName => $value) {
            if ($fieldName == 'sku') {
                continue;
            }
            $this->assertNotNull($value);
        }
    }

    /**
     * @param Item $orderItem
     * @param array $response
     * @return void
     */
    protected function assertOrderItem(Item $orderItem, array $response)
    {
        $this->assertEquals($orderItem->getId(), $response['item_id']);
        $this->assertEquals($orderItem->getOrderId(), $response['order_id']);
        $this->assertEquals($orderItem->getProductId(), $response['product_id']);
        $this->assertEquals($orderItem->getProductType(), $response['product_type']);
        $this->assertEquals($orderItem->getBasePrice(), $response['base_price']);
        $this->assertEquals($orderItem->getRowTotal(), $response['row_total']);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order_with_discount.php
     */
    public function testGetOrderWithDiscount()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);
        /** @var Item $orderItem */
        $orderItem = current($order->getItems());

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $orderItem->getId(),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get',
            ],
        ];

        $response = $this->_webApiCall($serviceInfo, ['id' => $orderItem->getId()]);

        $this->assertIsArray($response);
        $this->assertEquals(8.00, $response['row_total']);
        $this->assertEquals(8.00, $response['base_row_total']);
        $this->assertEquals(9.00, $response['row_total_incl_tax']);
        $this->assertEquals(9.00, $response['base_row_total_incl_tax']);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
