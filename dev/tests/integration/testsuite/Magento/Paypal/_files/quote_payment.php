<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Paypal\Model\Config;
use Magento\Paypal\Model\Express\Checkout;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->load('test01', 'reserved_order_id');

$payment = $quote->getPayment();
$payment->setMethod(Config::METHOD_WPP_EXPRESS)
    ->setAdditionalInformation(Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID, 123);
$quote->collectTotals()->save();
