<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Model\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 */
class PluginTest extends TestCase
{
    /**
     * Customer Account Service
     *
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerCreated()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Subscriber $subscriber */
        $subscriber = $objectManager->create(Subscriber::class);
        $subscriber->loadByEmail('customer_two@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(0, (int)$subscriber->getCustomerId());

        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = $objectManager->get(CustomerInterfaceFactory::class);
        $customerDataObject = $customerFactory->create()
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setEmail('customer_two@example.com');
        $createdCustomer = $this->customerRepository->save(
            $customerDataObject,
            $this->accountManagement->getPasswordHash('password')
        );

        $subscriber->loadByEmail('customer_two@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals((int)$createdCustomer->getId(), (int)$subscriber->getCustomerId());
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation enabled
     */
    public function testCustomerCreatedNotSubscribed()
    {
        $this->verifySubscriptionNotExist('customer@example.com');

        $objectManager = Bootstrap::getObjectManager();
        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = $objectManager->get(CustomerInterfaceFactory::class);
        $customerDataObject = $customerFactory->create()
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setEmail('customer@example.com');
        $this->accountManagement->createAccount($customerDataObject);

        $this->verifySubscriptionNotExist('customer@example.com');
    }

    /**
     * Verify a subscription doesn't exist for a given email address
     *
     * @param string $email
     * @return Subscriber
     */
    private function verifySubscriptionNotExist($email)
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Subscriber $subscriber */
        $subscriber = $objectManager->create(Subscriber::class);
        $subscriber->loadByEmail($email);
        $this->assertFalse($subscriber->isSubscribed());
        $this->assertEquals(0, (int)$subscriber->getId());
        return $subscriber;
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerUpdatedEmail()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Subscriber $subscriber */
        $subscriber = $objectManager->create(Subscriber::class);
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(1, (int)$subscriber->getCustomerId());

        $customer = $this->customerRepository->getById(1);
        $customer->setEmail('new@example.com');
        $this->customerRepository->save($customer);

        $subscriber->loadByEmail('new@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(1, (int)$subscriber->getCustomerId());
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedByIdAdminArea()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var Subscriber $subscriber */
        $subscriber = $objectManager->create(Subscriber::class);
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        $this->customerRepository->deleteById(1);

        $this->verifySubscriptionNotExist('customer@example.com');
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedAdminArea()
    {
        $customer = $this->customerRepository->getById(1);
        $objectManager = Bootstrap::getObjectManager();
        /** @var Subscriber $subscriber */
        $subscriber = $objectManager->create(Subscriber::class);
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->customerRepository->delete($customer);
        $this->verifySubscriptionNotExist('customer@example.com');
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation enabled
     */
    public function testCustomerWithZeroStoreIdIsSubscribed()
    {
        $objectManager = Bootstrap::getObjectManager();

        $currentStore = $objectManager->get(
            StoreManagerInterface::class
        )->getStore()->getId();

        $subscriber = $objectManager->create(Subscriber::class);
        /** @var Subscriber $subscriber */
        $subscriber->setStoreId($currentStore)
            ->setCustomerId(0)
            ->setSubscriberEmail('customer@example.com')
            ->setSubscriberStatus(Subscriber::STATUS_SUBSCRIBED)
            ->save();

        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = $objectManager->get(CustomerInterfaceFactory::class);
        $customerDataObject = $customerFactory->create()
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setStoreId(0)
            ->setEmail('customer@example.com');
        /** @var CustomerInterface $customer */
        $customer = $this->accountManagement->createAccount($customerDataObject);

        $this->customerRepository->save($customer);

        $subscriber->loadByEmail('customer@example.com');

        $this->assertEquals($customer->getId(), (int)$subscriber->getCustomerId());
        $this->assertEquals($currentStore, (int)$subscriber->getStoreId());
    }

    /**
     * Test get list customer, which have more then 2 subscribes in newsletter_subscriber.
     *
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerWithTwoNewsLetterSubscriptions()
    {
        /** @var SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchBuilder->addFilter('entity_id', 1)->create();
        $items = $this->customerRepository->getList($searchCriteria)->getItems();
        /** @var CustomerInterface $customer */
        $customer = $items[0];
        $extensionAttributes = $customer->getExtensionAttributes();
        $this->assertTrue($extensionAttributes->getIsSubscribed());
    }

    protected function setUp(): void
    {
        $this->accountManagement = Bootstrap::getObjectManager()->get(
            AccountManagementInterface::class
        );
        $this->customerRepository = Bootstrap::getObjectManager()->get(
            CustomerRepositoryInterface::class
        );
    }

    protected function tearDown(): void
    {
        /** @var CustomerRegistry $customerRegistry */
        $customerRegistry = Bootstrap::getObjectManager()
            ->get(CustomerRegistry::class);
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }
}
