<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Model\ResourceModel\Problem;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Newsletter\Model\Problem;
use Magento\Newsletter\Model\Subscriber;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collection;

    /**
     * @magentoDataFixture Magento/Newsletter/_files/problems.php
     */
    public function testAddCustomersData()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()
            ->create(CustomerRepositoryInterface::class);
        $customer = $customerRepository->getById(1);
        /** @var Subscriber $subscriber */
        $subscriber = Bootstrap::getObjectManager()
            ->create(Subscriber::class)->loadByEmail($customer->getEmail());
        /** @var Problem $problem */
        $problem = Bootstrap::getObjectManager()
            ->create(Problem::class)->addSubscriberData($subscriber);

        $item = $this->_collection->addSubscriberInfo()->load()->getFirstItem();

        $this->assertEquals($problem->getProblemErrorCode(), $item->getErrorCode());
        $this->assertEquals($problem->getProblemErrorText(), $item->getErrorText());
        $this->assertEquals($problem->getSubscriberId(), $item->getSubscriberId());
        $this->assertEquals($customer->getEmail(), $item->getSubscriberEmail());
        $this->assertEquals($customer->getFirstname(), $item->getCustomerFirstName());
        $this->assertEquals($customer->getLastname(), $item->getCustomerLastName());
        $this->assertStringContainsString($customer->getFirstname(), $item->getCustomerName());
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()
            ->create(Collection::class);
    }
}
