<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Contact\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Contact\Helper\Data
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $contactsHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Verify if username is set in session
     */
    public function testGetUserName()
    {
        $this->assertEquals('John Smith', $this->contactsHelper->getUserName());
    }

    /**
     * Verify if user email is set in session
     */
    public function testGetEmail()
    {
        $this->assertEquals('customer@example.com', $this->contactsHelper->getUserEmail());
    }

    /**
     * Setup customer data
     */
    protected function setUp(): void
    {
        $customerIdFromFixture = 1;
        $this->contactsHelper = Bootstrap::getObjectManager()->create(Data::class);
        $this->customerSession = Bootstrap::getObjectManager()->create(Session::class);
        /**
         * @var $customerRepository CustomerRepositoryInterface
         */
        $customerRepository = Bootstrap::getObjectManager()->create(
            CustomerRepositoryInterface::class
        );
        $customerData = $customerRepository->getById($customerIdFromFixture);
        $this->customerSession->setCustomerDataObject($customerData);
    }
}
