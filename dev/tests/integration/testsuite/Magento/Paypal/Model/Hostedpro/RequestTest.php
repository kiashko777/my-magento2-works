<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Model\Hostedpro;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package Magento\Paypal\Model
 */
class RequestTest extends TestCase
{
    /**
     * @var Request
     */
    private $model;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @covers \Magento\Paypal\Model\Hostedpro\Request::setOrder()
     * @magentoDataFixture Magento/Paypal/_files/order_hostedpro.php
     */
    public function testSetOrder()
    {
        $incrementId = '100000001';
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId($incrementId);

        $this->model->setOrder($order);
        $addressData = require(__DIR__ . '/../../_files/address_data.php');
        static::assertEquals($incrementId, $this->model->getInvoice());

        $this->assertAddress($addressData, 'billing');
        $this->assertAddress($addressData);
    }

    /**
     * Assert address details
     *
     * @param array $address
     * @param string $type
     */
    protected function assertAddress(array $address, $type = '')
    {
        $type = !empty($type) ? $type . '_' : '';

        static::assertEquals($address['firstname'], $this->model->getData($type . 'first_name'));
        static::assertEquals($address['lastname'], $this->model->getData($type . 'last_name'));
        static::assertEquals($address['city'], $this->model->getData($type . 'city'));
        static::assertEquals($address['region'], $this->model->getData($type . 'state'));
        static::assertEquals($address['country_id'], $this->model->getData($type . 'country'));
        static::assertEquals($address['postcode'], $this->model->getData($type . 'zip'));
        static::assertEquals($address['street'], $this->model->getData($type . 'address1'));
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->model = $this->objectManager->create(Request::class);
    }
}
