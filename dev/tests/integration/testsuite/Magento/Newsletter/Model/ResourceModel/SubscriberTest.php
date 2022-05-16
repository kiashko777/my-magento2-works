<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Model\ResourceModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SubscriberTest extends TestCase
{
    /**
     * @var Subscriber
     */
    protected $_resourceModel;

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testLoadByCustomerDataWithCustomerId()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()
            ->create(CustomerRepositoryInterface::class);
        $customerData = $customerRepository->getById(1);
        $result = $this->_resourceModel->loadByCustomerData($customerData);

        $this->assertEquals(1, $result['customer_id']);
        $this->assertEquals('customer@example.com', $result['subscriber_email']);
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     * @magentoDataFixture Magento/Customer/_files/two_customers.php
     */
    public function testLoadByCustomerDataWithoutCustomerId()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = Bootstrap::getObjectManager()
            ->create(CustomerRepositoryInterface::class);
        $customerData = $customerRepository->getById(2);
        $result = $this->_resourceModel->loadByCustomerData($customerData);

        $this->assertEquals(0, $result['customer_id']);
        $this->assertEquals('customer_two@example.com', $result['subscriber_email']);
    }

    protected function setUp(): void
    {
        $this->_resourceModel = Bootstrap::getObjectManager()
            ->create(Subscriber::class);
    }
}
