<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Controller\Adminhtml\Address;

use Magento\Backend\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class SaveTest extends AbstractBackendController
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AccountManagementInterface */
    private $accountManagement;

    /** @var ObjectManager */
    private $objectManager;

    /** @var Save */
    private $customerAddress;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     *
     * Check that customer id set and addresses saved
     */
    public function testSaveActionWithValidAddressData()
    {
        $customer = $this->customerRepository->get('customer5@example.com');
        $customerId = $customer->getId();
        $post = [
            'parent_id' => $customerId,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => ['test street'],
            'city' => 'test city',
            'region_id' => 10,
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
        ];
        $this->getRequest()->setPostValue($post)->setMethod(HttpRequest::METHOD_POST);

        $this->objectManager->get(Session::class)->setCustomerFormData($post);

        $this->customerAddress->execute();

        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        /** Check that customer data were cleaned after it was saved successfully*/
        $this->assertEmpty($this->objectManager->get(Session::class)->getCustomerData());

        $customer = $this->customerRepository->getById($customerId);

        $this->assertEquals('Firstname', $customer->getFirstname());
        $addresses = $customer->getAddresses();
        $this->assertCount(1, $addresses);
        $this->assertNull($this->accountManagement->getDefaultBillingAddress($customerId));
        $this->assertNull($this->accountManagement->getDefaultShippingAddress($customerId));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     *
     * Check that customer id set and addresses saved
     */
    public function testSaveActionWithDefaultShippingAndBilling()
    {
        $customer = $this->customerRepository->get('customer5@example.com');
        $customerId = $customer->getId();
        $post = [
            'parent_id' => $customerId,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => ['test street'],
            'city' => 'test city',
            'region_id' => 10,
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'default_billing' => true,
            'default_shipping' => true
        ];
        $this->getRequest()->setPostValue($post)->setMethod(HttpRequest::METHOD_POST);

        $this->objectManager->get(Session::class)->setCustomerFormData($post);

        $this->customerAddress->execute();
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        /**
         * Check that customer data were cleaned after it was saved successfully
         */
        $this->assertEmpty($this->objectManager->get(Session::class)->getCustomerData());

        /**
         * Remove stored customer from registry
         */
        $this->objectManager->get(CustomerRegistry::class)->remove($customerId);
        $customer = $this->customerRepository->get('customer5@example.com');
        $this->assertEquals('Firstname', $customer->getFirstname());
        $addresses = $customer->getAddresses();
        $this->assertCount(1, $addresses);

        $this->assertNotNull($this->accountManagement->getDefaultBillingAddress($customerId));
        $this->assertNotNull($this->accountManagement->getDefaultShippingAddress($customerId));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     *
     * Check that customer id set and addresses saved
     */
    public function testSaveActionWithExistingAdresses()
    {
        $customer = $this->customerRepository->get('customer@example.com');
        $customerId = $customer->getId();
        $post = [
            'parent_id' => $customerId,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => ['test street'],
            'city' => 'test city',
            'region_id' => 10,
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
        ];
        $this->getRequest()->setPostValue($post)->setMethod(HttpRequest::METHOD_POST);

        $this->objectManager->get(Session::class)->setCustomerFormData($post);

        $this->customerAddress->execute();
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        /**
         * Check that customer data were cleaned after it was saved successfully
         */
        $this->assertEmpty($this->objectManager->get(Session::class)->getCustomerData());

        $customer = $this->customerRepository->getById($customerId);

        $this->assertEquals('test firstname', $customer->getFirstname());
        $addresses = $customer->getAddresses();
        $this->assertCount(4, $addresses);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->customerRepository = Bootstrap::getObjectManager()->get(
            CustomerRepositoryInterface::class
        );
        $this->accountManagement = Bootstrap::getObjectManager()->get(
            AccountManagementInterface::class
        );
        $this->objectManager = Bootstrap::getObjectManager();
        $this->customerAddress = $this->objectManager->get(Save::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        /**
         * Unset customer data
         */
        Bootstrap::getObjectManager()->get(Session::class)->setCustomerData(null);

        /**
         * Unset messages
         */
        Bootstrap::getObjectManager()->get(Session::class)->getMessages(true);
    }
}
