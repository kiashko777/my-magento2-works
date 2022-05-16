<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order\Payment;

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Tests transaction model:
 *
 * @see \Magento\Sales\Model\Order\Payment\Transaction
 * @magentoDataFixture Magento/Sales/_files/transactions.php
 */
class TransactionTest extends TestCase
{
    public function testLoadByTxnId()
    {
        $order = Bootstrap::getObjectManager()->create(Order::class);
        $order->loadByIncrementId('100000001');

        /**
         * @var $repository \Magento\Sales\Model\Order\Payment\Transaction\Repository
         */
        $repository = Bootstrap::getObjectManager()->create(
            \Magento\Sales\Model\Order\Payment\Transaction\Repository::class
        );
        /**
         * @var $model Transaction
         */
        $model = $repository->getByTransactionId(
            'invalid_transaction_id',
            $order->getPayment()->getId(),
            $order->getId()
        );

        $this->assertFalse($model);

        $model = $repository->getByTransactionId('trx1', $order->getPayment()->getId(), $order->getId());
        $this->assertNotFalse($model->getId());
    }
}
