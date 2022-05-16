<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GiftMessage\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class OrderItemGetRepositoryTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'salesOrderItemRepositoryV1';
    const RESOURCE_PATH = '/V1/orders/items/';

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/order_with_message.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testGet()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Order $order */
        $order = $objectManager->create(Order::class)->loadByIncrementId('100000001');
        $items = $order->getItems();
        /** @var OrderItemInterface $orderItem */
        $orderItem = array_shift($items);
        $itemId = $orderItem->getItemId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $itemId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $expectedMessage = [
            'recipient' => 'Mercutio',
            'sender' => 'Romeo',
            'message' => 'I thought all for the best.',
        ];
        $requestData = ['id' => $itemId];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $resultMessage = $result['extension_attributes']['gift_message'];
        static::assertCount(5, $resultMessage);
        unset($resultMessage['gift_message_id']);
        unset($resultMessage['customer_id']);
        static::assertEquals($expectedMessage, $resultMessage);
    }
}
