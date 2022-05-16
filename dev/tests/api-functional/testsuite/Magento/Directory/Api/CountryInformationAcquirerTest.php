<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Directory\Api;

use Magento\Directory\Api\Data\CountryInformationInterface;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

class CountryInformationAcquirerTest extends WebapiAbstract
{
    const SERVICE_NAME = 'directoryCountryInformationAcquirerV1';
    const RESOURCE_COUNTRIES_PATH = '/V1/directory/countries';
    const RESOURCE_COUNTRY = 'US';
    const SERVICE_VERSION = 'V1';

    const STORE_CODE_FROM_FIXTURE = 'fixturestore';

    /**
     * Remove test store
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()
            ->get(Registry::class);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var $store Store */
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->load(self::STORE_CODE_FROM_FIXTURE);
        if ($store->getId()) {
            $store->delete();
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    /**
     * @magentoApiDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testGetCountries()
    {
        /** @var $store Group */
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->load(self::STORE_CODE_FROM_FIXTURE);
        $this->assertNotEmpty($store->getId(), 'Precondition failed: fixture store was not created.');

        $result = $this->getCountriesInfo(self::STORE_CODE_FROM_FIXTURE);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('two_letter_abbreviation', $result[0]);
        $this->assertArrayHasKey('three_letter_abbreviation', $result[0]);
        $this->assertArrayHasKey('full_name_locale', $result[0]);
        $this->assertArrayHasKey('full_name_english', $result[0]);

        $this->assertSame('AD', $result[0]['id']);
        $this->assertSame('AD', $result[0]['two_letter_abbreviation']);
        $this->assertSame('AND', $result[0]['three_letter_abbreviation']);
        $this->assertSame('Andorra', $result[0]['full_name_english']);
    }

    /**
     * Retrieve existing country information for the store
     *
     * @param string $storeCode
     * @return CountryInformationInterface
     */
    protected function getCountriesInfo($storeCode = 'default')
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_COUNTRIES_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCountriesInfo',
            ],
        ];
        $requestData = ['storeId' => $storeCode];

        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testGetCountry()
    {
        /** @var $store Group */
        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->load(self::STORE_CODE_FROM_FIXTURE);
        $this->assertNotEmpty($store->getId(), 'Precondition failed: fixture store was not created.');

        $result = $this->getCountryInfo(self::STORE_CODE_FROM_FIXTURE);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('two_letter_abbreviation', $result);
        $this->assertArrayHasKey('three_letter_abbreviation', $result);
        $this->assertArrayHasKey('full_name_locale', $result);
        $this->assertArrayHasKey('full_name_english', $result);
        $this->assertArrayHasKey('available_regions', $result);

        $this->assertSame('US', $result['id']);
        $this->assertSame('US', $result['two_letter_abbreviation']);
        $this->assertSame('USA', $result['three_letter_abbreviation']);
        $this->assertSame('United States', $result['full_name_english']);
    }

    /**
     * Retrieve existing country information for the store
     *
     * @param string $storeCode
     * @return CountryInformationInterface
     */
    protected function getCountryInfo($storeCode = 'default')
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_COUNTRIES_PATH . '/' . self::RESOURCE_COUNTRY,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetCountryInfo',
            ],
        ];
        $requestData = ['storeId' => $storeCode, 'countryId' => self::RESOURCE_COUNTRY];

        return $this->_webApiCall($serviceInfo, $requestData);
    }
}
