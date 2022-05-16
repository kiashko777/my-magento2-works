<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$customersToRemove = [
    'customer@search.example.com',
    'customer2@search.example.com',
    'customer3@search.example.com',
];

/**
 * @var Magento\Customer\Api\CustomerRepositoryInterface
 */
$customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
/**
 * @var RequestThrottler $throttler
 */
$throttler = Bootstrap::getObjectManager()->create(RequestThrottler::class);
foreach ($customersToRemove as $customerEmail) {
    try {
        $customer = $customerRepository->get($customerEmail);
        $customerRepository->delete($customer);
    } catch (NoSuchEntityException $exception) {
        /**
         * Tests which are wrapped with MySQL transaction clear all data by transaction rollback.
         */
        continue;
    }

    /* Unlock account if it was locked for tokens retrieval */
    $throttler->resetAuthenticationFailuresCount($customerEmail, RequestThrottler::USER_TYPE_CUSTOMER);
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
