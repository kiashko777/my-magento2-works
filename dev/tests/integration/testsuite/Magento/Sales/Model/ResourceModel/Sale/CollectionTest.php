<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\ResourceModel\Sale;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testSetCustomerFilter()
    {
        $collectionModel = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $this->assertEquals(1, $collectionModel->setCustomerIdFilter(1)->count());
        $collectionModel = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $this->assertEquals(0, $collectionModel->setCustomerIdFilter(2)->count());
    }
}
