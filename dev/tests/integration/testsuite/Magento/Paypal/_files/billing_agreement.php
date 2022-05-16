<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var Agreement $billingAgreement */

use Magento\Paypal\Model\Billing\Agreement;
use Magento\TestFramework\Helper\Bootstrap;

$billingAgreement = Bootstrap::getObjectManager()->create(
    Agreement::class
)->setAgreementLabel(
    'TEST'
)->setCustomerId(
    1
)->setMethodCode(
    'paypal_express'
)->setReferenceId(
    'REF-ID-TEST-678'
)->setStatus(
    Magento\Paypal\Model\Billing\Agreement::STATUS_ACTIVE
)->setStoreId(
    1
)->save();
