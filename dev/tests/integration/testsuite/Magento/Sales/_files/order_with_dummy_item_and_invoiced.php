<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');
/** @var Order $order */
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

$orderItems = [
    [
        OrderItemInterface::PRODUCT_ID => 2,
        OrderItemInterface::BASE_PRICE => 100,
        OrderItemInterface::ORDER_ID => $order->getId(),
        OrderItemInterface::QTY_ORDERED => 2,
        OrderItemInterface::QTY_INVOICED => 2,
        OrderItemInterface::PRICE => 100,
        OrderItemInterface::ROW_TOTAL => 102,
        OrderItemInterface::PRODUCT_TYPE => 'bundle',
        'children' => [
            [
                OrderItemInterface::PRODUCT_ID => 13,
                OrderItemInterface::ORDER_ID => $order->getId(),
                OrderItemInterface::QTY_ORDERED => 2,
                OrderItemInterface::QTY_INVOICED => 2,
                OrderItemInterface::BASE_PRICE => 90,
                OrderItemInterface::PRICE => 90,
                OrderItemInterface::ROW_TOTAL => 92,
                OrderItemInterface::PRODUCT_TYPE => 'simple',
                'product_options' => [
                    'bundle_selection_attributes' => '{"qty":2}',
                ],
            ]
        ],
    ]
];

// Invoiced all existing order items.
foreach ($order->getAllItems() as $item) {
    $item->setQtyInvoiced(1);
    $item->save();
}

saveOrderItems($orderItems);

/**
 * Save Order Items.
 *
 * @param array $orderItems
 * @param Item|null $parentOrderItem [optional]
 * @return void
 */
function saveOrderItems(array $orderItems, $parentOrderItem = null)
{
    /** @var array $orderItemData */
    foreach ($orderItems as $orderItemData) {
        /** @var $orderItem Item */
        $orderItem = Bootstrap::getObjectManager()->create(
            Item::class
        );
        if (null !== $parentOrderItem) {
            $orderItemData['parent_item'] = $parentOrderItem;
        }
        $orderItem
            ->setData($orderItemData)
            ->save();

        if (isset($orderItemData['children'])) {
            saveOrderItems($orderItemData['children'], $orderItem);
        }
    }
}
