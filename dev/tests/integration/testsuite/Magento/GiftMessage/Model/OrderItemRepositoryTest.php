<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftMessage\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\ObjectManagerInterface;
use Magento\GiftMessage\Api\Data\MessageInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class OrderItemRepositoryTest extends TestCase
{
    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var Message */
    protected $message;

    /** @var OrderItemRepository */
    protected $giftMessageOrderItemRepository;

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/order_with_message.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     */
    public function testGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $message = $this->giftMessageOrderItemRepository->get($order->getEntityId(), $orderItem->getItemId());
        $this->assertEquals('Romeo', $message->getSender());
        $this->assertEquals('Mercutio', $message->getRecipient());
        $this->assertEquals('I thought all for the best.', $message->getMessage());
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/order_with_message.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     */
    public function testGetNoProvidedItemId()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No item with the provided ID was found in the Order. Verify the ID and try again.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $this->giftMessageOrderItemRepository->get($order->getEntityId(), $orderItem->getItemId() * 10);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     */
    public function testSave()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $result = $this->giftMessageOrderItemRepository->save(
            $order->getEntityId(),
            $orderItem->getItemId(),
            $this->message
        );

        $message = $this->giftMessageOrderItemRepository->get($order->getEntityId(), $orderItem->getItemId());

        $this->assertTrue($result);
        $this->assertEquals('Romeo', $message->getSender());
        $this->assertEquals('Mercutio', $message->getRecipient());
        $this->assertEquals('I thought all for the best.', $message->getMessage());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 0
     */
    public function testSaveMessageIsNotAvailable()
    {
        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('The gift message isn\'t available.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $this->giftMessageOrderItemRepository->save($order->getEntityId(), $orderItem->getItemId(), $this->message);
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/virtual_order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     */
    public function testSaveMessageIsVirtual()
    {
        $this->expectException(InvalidTransitionException::class);
        $this->expectExceptionMessage('Gift messages can\'t be used for virtual products.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $this->giftMessageOrderItemRepository->save($order->getEntityId(), $orderItem->getItemId(), $this->message);
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/empty_order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_items 1
     */
    public function testSaveMessageNoProvidedItemId()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No item with the provided ID was found in the Order. Verify the ID and try again.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems();
        $orderItem = array_shift($orderItem);

        /** @var MessageInterface $message */
        $this->giftMessageOrderItemRepository->save(
            $order->getEntityId(),
            $orderItem->getItemId() * 10,
            $this->message
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->message = $this->objectManager->create(Message::class);
        $this->message->setSender('Romeo');
        $this->message->setRecipient('Mercutio');
        $this->message->setMessage('I thought all for the best.');

        $this->giftMessageOrderItemRepository = $this->objectManager->create(
            OrderItemRepository::class
        );
    }

    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->message = null;
        $this->giftMessageOrderItemRepository = null;
    }
}
