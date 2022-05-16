<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftMessage\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\ObjectManagerInterface;
use Magento\GiftMessage\Api\Data\MessageInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var Message */
    protected $message;

    /** @var OrderRepository */
    protected $giftMessageOrderRepository;

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/order_with_message.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 1
     */
    public function testGet()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $message = $this->giftMessageOrderRepository->get($order->getEntityId());
        $this->assertEquals('Romeo', $message->getSender());
        $this->assertEquals('Mercutio', $message->getRecipient());
        $this->assertEquals('I thought all for the best.', $message->getMessage());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 1
     */
    public function testSave()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $result = $this->giftMessageOrderRepository->save($order->getEntityId(), $this->message);

        $message = $this->giftMessageOrderRepository->get($order->getEntityId());

        $this->assertTrue($result);
        $this->assertEquals('Romeo', $message->getSender());
        $this->assertEquals('Mercutio', $message->getRecipient());
        $this->assertEquals('I thought all for the best.', $message->getMessage());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 0
     */
    public function testSaveMessageIsNotAvailable()
    {
        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('The gift message isn\'t available.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $this->giftMessageOrderRepository->save($order->getEntityId(), $this->message);
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/virtual_order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 1
     */
    public function testSaveMessageIsVirtual()
    {
        $this->expectException(InvalidTransitionException::class);
        $this->expectExceptionMessage('Gift messages can\'t be used for virtual products.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $this->giftMessageOrderRepository->save($order->getEntityId(), $this->message);
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/empty_order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 1
     */
    public function testSaveMessageIsEmpty()
    {
        $this->expectException(InputException::class);

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $this->giftMessageOrderRepository->save($order->getEntityId(), $this->message);

        $this->expectExceptionMessage(
            "Gift messages can't be used for an empty order. Create an order, add an item, and try again."
        );
    }

    /**
     * @magentoDataFixture Magento/GiftMessage/_files/empty_order.php
     * @magentoConfigFixture default_store sales/gift_options/allow_order 1
     */
    public function testSaveMessageNoProvidedItemId()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No order exists with this ID. Verify your information and try again.');

        /** @var Order $order */
        $order = $this->objectManager->create(Order::class)->loadByIncrementId('100000001');

        /** @var MessageInterface $message */
        $this->giftMessageOrderRepository->save($order->getEntityId() * 10, $this->message);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->message = $this->objectManager->create(Message::class);
        $this->message->setSender('Romeo');
        $this->message->setRecipient('Mercutio');
        $this->message->setMessage('I thought all for the best.');

        $this->giftMessageOrderRepository = $this->objectManager->create(
            OrderRepository::class
        );
    }

    protected function tearDown(): void
    {
        $this->objectManager = null;
        $this->message = null;
        $this->giftMessageOrderRepository = null;
    }
}
