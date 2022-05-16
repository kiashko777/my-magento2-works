<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for customer addresses collection
 */

namespace Magento\Customer\Model\ResourceModel\Address;

use Magento\Customer\Model\Customer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testSetCustomerFilter()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $select = $collection->getSelect();
        $this->assertSame($collection, $collection->setCustomerFilter([1, 2]));
        $customer = Bootstrap::getObjectManager()->create(
            Customer::class
        );
        $collection->setCustomerFilter($customer);
        $customer->setId(3);
        $collection->setCustomerFilter($customer);
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Sparent_id%S IN(%S1%S, %S2%S))%SAND%S(%Sparent_id%S = %S-1%S)%SAND%S(%Sparent_id%S = %S3%S)%A',
            (string)$select
        );
    }
}
