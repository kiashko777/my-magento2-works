<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class OrderGetStatusTest
 * @package Magento\Sales\Service\V1
 */
class OrderGetStatusTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders/%d/statuses';

    const SERVICE_READ_NAME = 'salesOrderManagementV1';

    const SERVICE_VERSION = 'V1';

    const ORDER_INCREMENT_ID = '100000001';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderGetStatus()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf(self::RESOURCE_PATH, $order->getId()),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getStatus',
            ],
        ];

        $this->assertEquals($order->getStatus(), $this->_webApiCall($serviceInfo, ['id' => $order->getId()]));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
