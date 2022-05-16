<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Api;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\Address;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for service layer \Magento\Customer\Model\ResourceModel\AddressRepository
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddressRepositoryTest extends TestCase
{
    /** @var  DataObjectHelper */
    protected $dataObjectHelper;
    /** @var AddressRepositoryInterface */
    private $repository;
    /** @var ObjectManagerInterface */
    private $_objectManager;
    /** @var Address[] */
    private $_expectedAddresses;
    /** @var AddressInterfaceFactory */
    private $_addressFactory;

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoDataFixture  Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSaveAddressChanges()
    {
        $address = $this->repository->getById(2);

        $address->setRegion($address->getRegion());
        // change phone #
        $address->setTelephone('555' . $address->getTelephone());
        $address = $this->repository->save($address);
        $this->assertEquals(2, $address->getId());

        $savedAddress = $this->repository->getById(2);
        $this->assertNotEquals($this->_expectedAddresses[1]->getTelephone(), $savedAddress->getTelephone());
    }

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoDataFixture  Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSaveAddressesIdSetButNotAlreadyExisting()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with addressId = 4200');

        $proposedAddress = $this->_createSecondAddress()->setId(4200);
        $this->repository->save($proposedAddress);
    }

    /**
     * Helper function that returns an Address Data Object that matches the data from customer_two_address fixture
     *
     * @return AddressInterface
     */
    private function _createSecondAddress()
    {
        $address = $this->_addressFactory->create();
        $this->dataObjectHelper->mergeDataObjects(
            AddressInterface::class,
            $address,
            $this->_expectedAddresses[1]
        );
        $address->setId(null);
        $address->setRegion($this->_expectedAddresses[1]->getRegion());
        return $address;
    }

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoDataFixture  Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testGetAddressById()
    {
        $addressId = 2;
        $address = $this->repository->getById($addressId);
        $this->assertEquals($this->_expectedAddresses[1], $address);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetAddressByIdBadAddressId()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with addressId = 12345');

        $this->repository->getById(12345);
    }

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddress()
    {
        $proposedAddress = $this->_createSecondAddress()->setCustomerId(1);

        $returnedAddress = $this->repository->save($proposedAddress);
        $this->assertNotNull($returnedAddress->getId());

        $savedAddress = $this->repository->getById($returnedAddress->getId());

        $expectedNewAddress = $this->_expectedAddresses[1];
        $expectedNewAddress->setId($savedAddress->getId());
        $expectedNewAddress->setRegion($this->_expectedAddresses[1]->getRegion());

        $this->assertEquals($expectedNewAddress->getExtensionAttributes(), $savedAddress->getExtensionAttributes());
        $this->assertEquals(
            $expectedNewAddress->getRegion()->getExtensionAttributes(),
            $savedAddress->getRegion()->getExtensionAttributes()
        );

        $this->assertEquals($expectedNewAddress, $savedAddress);
    }

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddressWithAttributes()
    {
        $proposedAddress = $this->_createFirstAddress()
            ->setCustomAttribute('firstname', 'Jane')
            ->setCustomAttribute('id', 4200)
            ->setCustomAttribute('weird', 'something_strange_with_hair')
            ->setId(null)
            ->setCustomerId(1);

        $returnedAddress = $this->repository->save($proposedAddress);

        $savedAddress = $this->repository->getById($returnedAddress->getId());
        $this->assertNotEquals($proposedAddress, $savedAddress);
        $this->assertArrayNotHasKey(
            'weird',
            $savedAddress->getCustomAttributes(),
            'Only valid attributes should be available.'
        );
    }

    /**
     * Helper function that returns an Address Data Object that matches the data from customer_address fixture
     *
     * @return AddressInterface
     */
    private function _createFirstAddress()
    {
        $address = $this->_addressFactory->create();
        $this->dataObjectHelper->mergeDataObjects(
            AddressInterface::class,
            $address,
            $this->_expectedAddresses[0]
        );
        $address->setId(null);
        $address->setRegion($this->_expectedAddresses[0]->getRegion());
        return $address;
    }

    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewInvalidAddress()
    {
        $address = $this->_createFirstAddress()
            ->setCustomAttribute('firstname', null)
            ->setId(null)
            ->setFirstname(null)
            ->setLastname(null)
            ->setCustomerId(1)
            ->setRegionId($invalidRegion = 10354);
        try {
            $this->repository->save($address);
        } catch (InputException $exception) {
            $this->assertEquals('One or more input exceptions have occurred.', $exception->getMessage());
            $errors = $exception->getErrors();
            $this->assertCount(3, $errors);
            $this->assertEquals('"firstname" is required. Enter and try again.', $errors[0]->getLogMessage());
            $this->assertEquals('"lastname" is required. Enter and try again.', $errors[1]->getLogMessage());
            $this->assertEquals(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['fieldName' => 'regionId', 'value' => $invalidRegion]
                ),
                $errors[2]->getLogMessage()
            );
        }

        $address->setCountryId($invalidCountry = 'invalid_id');
        try {
            $this->repository->save($address);
        } catch (InputException $exception) {
            $this->assertEquals(
                'One or more input exceptions have occurred.',
                $exception->getMessage()
            );
            $errors = $exception->getErrors();
            $this->assertCount(3, $errors);
            $this->assertEquals(
                '"firstname" is required. Enter and try again.',
                $errors[0]->getLogMessage()
            );
            $this->assertEquals(
                '"lastname" is required. Enter and try again.',
                $errors[1]->getLogMessage()
            );
            $this->assertEquals(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['fieldName' => 'countryId', 'value' => $invalidCountry]
                ),
                $errors[2]->getLogMessage()
            );
        }
    }

    public function testSaveAddressesCustomerIdNotExist()
    {
        $proposedAddress = $this->_createSecondAddress()->setCustomerId(4200);
        try {
            $this->repository->save($proposedAddress);
            $this->fail('Expected exception not thrown');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals('No such entity with customerId = 4200', $nsee->getMessage());
        }
    }

    public function testSaveAddressesCustomerIdInvalid()
    {
        $proposedAddress = $this->_createSecondAddress()->setCustomerId('this_is_not_a_valid_id');
        try {
            $this->repository->save($proposedAddress);
            $this->fail('Expected exception not thrown');
        } catch (NoSuchEntityException $nsee) {
            $this->assertEquals('No such entity with customerId = this_is_not_a_valid_id', $nsee->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testDeleteAddress()
    {
        $addressId = 1;
        // See that customer already has an address with expected addressId
        $addressDataObject = $this->repository->getById($addressId);
        $this->assertEquals($addressDataObject->getId(), $addressId);

        // Delete the address from the customer
        $this->repository->delete($addressDataObject);

        // See that address is deleted
        try {
            $addressDataObject = $this->repository->getById($addressId);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with addressId = 1', $exception->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testDeleteAddressById()
    {
        $addressId = 1;
        // See that customer already has an address with expected addressId
        $addressDataObject = $this->repository->getById($addressId);
        $this->assertEquals($addressDataObject->getId(), $addressId);

        // Delete the address from the customer
        $this->repository->deleteById($addressId);

        // See that address is deleted
        try {
            $addressDataObject = $this->repository->getById($addressId);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with addressId = 1', $exception->getMessage());
        }
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testDeleteAddressFromCustomerBadAddressId()
    {
        try {
            $this->repository->deleteById(12345);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with addressId = 12345', $exception->getMessage());
        }
    }

    /**
     * @param Filter[] $filters
     * @param Filter[] $filterGroup
     * @param array $expectedResult array of expected results indexed by ID
     *
     * @dataProvider searchAddressDataProvider
     *
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_two_addresses.php
     * @magentoAppIsolation enabled
     */
    public function testSearchAddresses($filters, $filterGroup, $expectedResult)
    {
        /** @var SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = $this->_objectManager->create(SearchCriteriaBuilder::class);
        foreach ($filters as $filter) {
            $searchBuilder->addFilters([$filter]);
        }
        if ($filterGroup !== null) {
            $searchBuilder->addFilters($filterGroup);
        }

        $searchResults = $this->repository->getList($searchBuilder->create());

        $this->assertEquals(count($expectedResult), $searchResults->getTotalCount());

        /** @var AddressInterface $item */
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals(
                $expectedResult[$item->getId()]['city'],
                $item->getCity()
            );
            $this->assertEquals(
                $expectedResult[$item->getId()]['postcode'],
                $item->getPostcode()
            );
            $this->assertEquals(
                $expectedResult[$item->getId()]['firstname'],
                $item->getFirstname()
            );
            unset($expectedResult[$item->getId()]);
        }
    }

    public function searchAddressDataProvider()
    {
        /**
         * @var FilterBuilder $filterBuilder
         */
        $filterBuilder = Bootstrap::getObjectManager()
            ->create(FilterBuilder::class);
        return [
            'Address with postcode 75477' => [
                [$filterBuilder->setField('postcode')->setValue('75477')->create()],
                null,
                [1 => ['city' => 'CityM', 'postcode' => 75477, 'firstname' => 'John']],
            ],
            'Address with city CityM' => [
                [$filterBuilder->setField('city')->setValue('CityM')->create()],
                null,
                [1 => ['city' => 'CityM', 'postcode' => 75477, 'firstname' => 'John']],
            ],
            'Addresses with firstname John' => [
                [$filterBuilder->setField('firstname')->setValue('John')->create()],
                null,
                [
                    1 => ['city' => 'CityM', 'postcode' => 75477, 'firstname' => 'John'],
                    2 => ['city' => 'CityX', 'postcode' => 47676, 'firstname' => 'John']
                ],
            ],
            'Addresses with postcode of either 75477 or 47676' => [
                [],
                [
                    $filterBuilder->setField('postcode')->setValue('75477')->create(),
                    $filterBuilder->setField('postcode')->setValue('47676')->create()
                ],
                [
                    1 => ['city' => 'CityM', 'postcode' => 75477, 'firstname' => 'John'],
                    2 => ['city' => 'CityX', 'postcode' => 47676, 'firstname' => 'John']
                ],
            ],
            'Addresses with postcode greater than 0' => [
                [$filterBuilder->setField('postcode')->setValue('0')->setConditionType('gt')->create()],
                null,
                [
                    1 => ['city' => 'CityM', 'postcode' => 75477, 'firstname' => 'John'],
                    2 => ['city' => 'CityX', 'postcode' => 47676, 'firstname' => 'John']
                ],
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->repository = $this->_objectManager->create(AddressRepositoryInterface::class);
        $this->_addressFactory = $this->_objectManager->create(
            AddressInterfaceFactory::class
        );
        $this->dataObjectHelper = $this->_objectManager->create(DataObjectHelper::class);

        $regionFactory = $this->_objectManager->create(RegionInterfaceFactory::class);
        $region = $regionFactory->create();
        $region->setRegionCode('AL')
            ->setRegion('Alabama')
            ->setRegionId(1);
        $address = $this->_addressFactory->create()
            ->setId('1')
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('75477')
            ->setRegion($region)
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith')
            ->setCompany('CompanyName')
            ->setRegionId(1);

        /* XXX: would it be better to have a clear method for this? */
        $address2 = $this->_addressFactory->create()
            ->setId('2')
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('47676')
            ->setRegion($region)
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith')
            ->setRegionId(1);

        $this->_expectedAddresses = [$address, $address2];
    }

    protected function tearDown(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var CustomerRegistry $customerRegistry */
        $customerRegistry = $objectManager->get(CustomerRegistry::class);
        $customerRegistry->remove(1);
    }
}
