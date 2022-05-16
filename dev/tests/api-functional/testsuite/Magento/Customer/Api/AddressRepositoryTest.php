<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class AddressRepositoryTest extends WebapiAbstract
{
    const SOAP_SERVICE_NAME = 'customerAddressRepositoryV1';
    const SOAP_SERVICE_VERSION = 'V1';

    /** @var AddressRepositoryInterface */
    protected $addressRepository;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetAddress()
    {
        $fixtureAddressId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customers/addresses/{$fixtureAddressId}",
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetById',
            ],
        ];
        $requestData = ['addressId' => $fixtureAddressId];
        $addressData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $this->getFirstFixtureAddressData(),
            $addressData,
            "Address data is invalid."
        );
    }

    /**
     * Retrieve data of the first fixture address.
     *
     * @return array
     */
    protected function getFirstFixtureAddressData()
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Smith',
            'city' => 'CityM',
            'country_id' => 'US',
            'company' => 'CompanyName',
            'postcode' => '75477',
            'telephone' => '3468676',
            'street' => ['Green str, 67'],
            'id' => 1,
            'default_billing' => true,
            'default_shipping' => true,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
            'region_id' => 1,
        ];
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testDeleteAddress()
    {
        $fixtureAddressId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/addresses/{$fixtureAddressId}",
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'DeleteById',
            ],
        ];
        $requestData = ['addressId' => $fixtureAddressId];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response, 'Expected response should be true.');

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with addressId = 1');
        $this->addressRepository->getById($fixtureAddressId);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerRepository = $objectManager->get(
            CustomerRepositoryInterface::class
        );
        $this->addressRepository = $objectManager->get(
            AddressRepositoryInterface::class
        );
        parent::setUp();
    }

    /**
     * Ensure that fixture customer and his addresses are deleted.
     */
    protected function tearDown(): void
    {
        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        try {
            $fixtureFirstAddressId = 1;
            $this->addressRepository->deleteById($fixtureFirstAddressId);
        } catch (NoSuchEntityException $e) {
            /** First address fixture was not used */
        }
        try {
            $fixtureSecondAddressId = 2;
            $this->addressRepository->deleteById($fixtureSecondAddressId);
        } catch (NoSuchEntityException $e) {
            /** Second address fixture was not used */
        }
        try {
            $fixtureCustomerId = 1;
            $this->customerRepository->deleteById($fixtureCustomerId);
        } catch (NoSuchEntityException $e) {
            /** Customer fixture was not used */
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
        parent::tearDown();
    }

    /**
     * Retrieve data of the second fixture address.
     *
     * @return array
     */
    protected function getSecondFixtureAddressData()
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Smith',
            'city' => 'CityX',
            'country_id' => 'US',
            'postcode' => '47676',
            'telephone' => '3234676',
            'street' => ['Black str, 48'],
            'id' => 2,
            'default_billing' => false,
            'default_shipping' => false,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
            'region_id' => 1,
        ];
    }
}
