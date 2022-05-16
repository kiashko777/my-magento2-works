<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * API test for creation of Creditmemo for certain Order.
 */
class RefundOrderTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'salesRefundOrderV1';
    const SERVICE_VERSION = 'V1';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order_with_shipping_and_invoice.php
     */
    public function testShortRequest()
    {
        /** @var Order $existingOrder */
        $existingOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId('100000001');

        $result = $this->_webApiCall(
            $this->getServiceData($existingOrder),
            ['orderId' => $existingOrder->getEntityId()]
        );

        $this->assertNotEmpty(
            $result,
            'Failed asserting that the received response is correct'
        );

        /** @var Order $updatedOrder */
        $updatedOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId($existingOrder->getIncrementId());

        try {
            $creditmemo = $this->creditmemoRepository->get($result);

            $expectedItems = $this->getOrderItems($existingOrder);
            $actualCreditmemoItems = $this->getCreditmemoItems($creditmemo);
            $actualRefundedOrderItems = $this->getRefundedOrderItems($updatedOrder);

            $this->assertEquals(
                $expectedItems,
                $actualCreditmemoItems,
                'Failed asserting that the Creditmemo contains all requested items'
            );

            $this->assertEquals(
                $expectedItems,
                $actualRefundedOrderItems,
                'Failed asserting that all requested order items were refunded'
            );

            $this->assertEquals(
                $creditmemo->getShippingAmount(),
                $existingOrder->getShippingAmount(),
                'Failed asserting that the Creditmemo contains correct shipping amount'
            );

            $this->assertEquals(
                $creditmemo->getShippingAmount(),
                $updatedOrder->getShippingRefunded(),
                'Failed asserting that proper shipping amount of the Order was refunded'
            );

            $this->assertEquals(
                Order::STATE_COMPLETE,
                $updatedOrder->getStatus(),
                'Failed asserting that order status has not changed'
            );
        } catch (NoSuchEntityException $e) {
            $this->fail('Failed asserting that Creditmemo was created');
        }
    }

    /**
     * Prepares and returns info for API service.
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    private function getServiceData(OrderInterface $order)
    {
        return [
            'rest' => [
                'resourcePath' => '/V1/order/' . $order->getEntityId() . '/refund',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'execute',
            ]
        ];
    }

    /**
     * Gets all items of given Order in proper format.
     *
     * @param Order $order
     *
     * @return array
     */
    private function getOrderItems(Order $order)
    {
        $items = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getAllItems() as $item) {
            $items[] = [
                'order_item_id' => $item->getItemId(),
                'qty' => $item->getQtyOrdered(),
            ];
        }

        return $items;
    }

    /**
     * Gets all items of given Creditmemo in proper format.
     *
     * @param CreditmemoInterface $creditmemo
     *
     * @return array
     */
    private function getCreditmemoItems(CreditmemoInterface $creditmemo)
    {
        $items = [];

        /** @var CreditmemoItemInterface $item */
        foreach ($creditmemo->getItems() as $item) {
            $items[] = [
                'order_item_id' => $item->getOrderItemId(),
                'qty' => $item->getQty(),
            ];
        }

        return $items;
    }

    /**
     * Gets refunded items of given Order in proper format.
     *
     * @param Order $order
     *
     * @return array
     */
    private function getRefundedOrderItems(Order $order)
    {
        $items = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getQtyRefunded() > 0) {
                $items[] = [
                    'order_item_id' => $item->getItemId(),
                    'qty' => $item->getQtyRefunded(),
                ];
            }
        }

        return $items;
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order_with_shipping_and_invoice.php
     */
    public function testFullRequest()
    {
        /** @var Order $existingOrder */
        $existingOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId('100000001');

        $expectedItems = $this->getOrderItems($existingOrder);
        $expectedItems[0]['qty'] = $expectedItems[0]['qty'] - 1;

        $expectedComment = [
            'comment' => 'Test Comment',
            'is_visible_on_front' => 1
        ];

        $expectedShippingAmount = 15;
        $expectedAdjustmentPositive = 5.53;
        $expectedAdjustmentNegative = 5.53;

        $result = $this->_webApiCall(
            $this->getServiceData($existingOrder),
            [
                'orderId' => $existingOrder->getEntityId(),
                'items' => $expectedItems,
                'comment' => $expectedComment,
                'arguments' => [
                    'shipping_amount' => $expectedShippingAmount,
                    'adjustment_positive' => $expectedAdjustmentPositive,
                    'adjustment_negative' => $expectedAdjustmentNegative
                ]
            ]
        );

        $this->assertNotEmpty(
            $result,
            'Failed asserting that the received response is correct'
        );

        /** @var Order $updatedOrder */
        $updatedOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId($existingOrder->getIncrementId());

        try {
            $creditmemo = $this->creditmemoRepository->get($result);

            $actualCreditmemoItems = $this->getCreditmemoItems($creditmemo);
            $actualCreditmemoComment = $this->getRecentComment($creditmemo);
            $actualRefundedOrderItems = $this->getRefundedOrderItems($updatedOrder);

            $this->assertEquals(
                $expectedItems,
                $actualCreditmemoItems,
                'Failed asserting that the Creditmemo contains all requested items'
            );

            $this->assertEquals(
                $expectedItems,
                $actualRefundedOrderItems,
                'Failed asserting that all requested order items were refunded'
            );

            $this->assertEquals(
                $expectedComment,
                $actualCreditmemoComment,
                'Failed asserting that the Creditmemo contains correct comment'
            );

            $this->assertEquals(
                $expectedShippingAmount,
                $creditmemo->getShippingAmount(),
                'Failed asserting that the Creditmemo contains correct shipping amount'
            );

            $this->assertEquals(
                $expectedShippingAmount,
                $updatedOrder->getShippingRefunded(),
                'Failed asserting that proper shipping amount of the Order was refunded'
            );

            $this->assertEquals(
                $expectedAdjustmentPositive,
                $creditmemo->getAdjustmentPositive(),
                'Failed asserting that the Creditmemo contains correct positive adjustment'
            );

            $this->assertEquals(
                $expectedAdjustmentNegative,
                $creditmemo->getAdjustmentNegative(),
                'Failed asserting that the Creditmemo contains correct negative adjustment'
            );

            $this->assertEquals(
                $existingOrder->getStatus(),
                $updatedOrder->getStatus(),
                'Failed asserting that order status was NOT changed'
            );
        } catch (NoSuchEntityException $e) {
            $this->fail('Failed asserting that Creditmemo was created');
        }
    }

    /**
     * Gets the most recent comment of given Creditmemo in proper format.
     *
     * @param CreditmemoInterface $creditmemo
     *
     * @return array|null
     */
    private function getRecentComment(CreditmemoInterface $creditmemo)
    {
        $comments = $creditmemo->getComments();

        if ($comments) {
            $comment = reset($comments);

            return [
                'comment' => $comment->getComment(),
                'is_visible_on_front' => $comment->getIsVisibleOnFront(),
            ];
        }

        return null;
    }

    /**
     * Test order will keep same(custom) status after partial refund, if state has not been changed.
     *
     * @magentoApiDataFixture Magento/Sales/_files/order_with_invoice_and_custom_status.php
     */
    public function testOrderStatusPartialRefund()
    {
        /** @var Order $existingOrder */
        $existingOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId('100000001');

        $items = $this->getOrderItems($existingOrder);
        $items[0]['qty'] -= 1;
        $result = $this->_webApiCall(
            $this->getServiceData($existingOrder),
            [
                'orderId' => $existingOrder->getEntityId(),
                'items' => $items,
            ]
        );

        $this->assertNotEmpty(
            $result,
            'Failed asserting that the received response is correct'
        );

        /** @var Order $updatedOrder */
        $updatedOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId($existingOrder->getIncrementId());

        $this->assertSame('custom_processing', $updatedOrder->getStatus());
        $this->assertSame('processing', $updatedOrder->getState());
    }

    /**
     * Test order will change custom status after total refund, when state has been changed.
     *
     * @magentoApiDataFixture Magento/Sales/_files/order_with_invoice_and_custom_status.php
     */
    public function testOrderStatusTotalRefund()
    {
        /** @var Order $existingOrder */
        $existingOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId('100000001');

        $items = $this->getOrderItems($existingOrder);
        $result = $this->_webApiCall(
            $this->getServiceData($existingOrder),
            [
                'orderId' => $existingOrder->getEntityId(),
                'items' => $items,
            ]
        );

        $this->assertNotEmpty(
            $result,
            'Failed asserting that the received response is correct'
        );

        /** @var Order $updatedOrder */
        $updatedOrder = $this->objectManager->create(Order::class)
            ->loadByIncrementId($existingOrder->getIncrementId());

        $this->assertSame('complete', $updatedOrder->getStatus());
        $this->assertSame('complete', $updatedOrder->getState());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->creditmemoRepository = $this->objectManager->get(
            CreditmemoRepositoryInterface::class
        );
    }
}
