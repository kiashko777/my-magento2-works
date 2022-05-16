<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Shopcart;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class GridTestAbstract extends TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();

        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
        $customerData = $customerRepository->getById(1);

        /** @var Quote $quoteFixture */
        $quoteFixture = $objectManager->create(Quote::class);
        $quoteFixture->load('test01', 'reserved_order_id');
        $quoteFixture->setIsActive(true);
        $quoteFixture->setCustomer($customerData);
        $quoteFixture->save();
    }
}
