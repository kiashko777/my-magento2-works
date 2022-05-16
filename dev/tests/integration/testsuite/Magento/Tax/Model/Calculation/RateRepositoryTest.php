<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\Calculation;

use Exception;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Api\Data\TaxRateInterface;
use Magento\Tax\Api\Data\TaxRateInterfaceFactory;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Model\TaxRuleFixtureFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RateRepositoryTest extends TestCase
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * TaxRate factory
     *
     * @var TaxRateInterfaceFactory
     */
    private $taxRateFactory;

    /**
     * TaxRateService
     *
     * @var TaxRateRepositoryInterface
     */
    private $rateRepository;

    /**
     * Helps in creating required tax rules.
     *
     * @var TaxRuleFixtureFactory
     */
    private $taxRateFixtureFactory;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var  RegionFactory
     */
    private $regionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @magentoDbIsolation enabled
     */
    public function testSave()
    {
        $taxData = [
            'tax_country_id' => 'US',
            'tax_region_id' => '8',
            'rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_is_range' => true,
            'zip_from' => 78765,
            'zip_to' => 78780,
        ];
        // Tax rate data object created
        $taxRate = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray($taxRate, $taxData, TaxRateInterface::class);
        //Tax rate service call
        $taxRateServiceData = $this->rateRepository->save($taxRate);

        //Assertions
        $this->assertInstanceOf(TaxRateInterface::class, $taxRateServiceData);
        $this->assertEquals($taxData['tax_country_id'], $taxRateServiceData->getTaxCountryId());
        $this->assertEquals($taxData['tax_region_id'], $taxRateServiceData->getTaxRegionId());
        $this->assertEquals($taxData['rate'], $taxRateServiceData->getRate());
        $this->assertEquals($taxData['code'], $taxRateServiceData->getCode());
        $this->assertEquals($taxData['zip_from'], $taxRateServiceData->getZipFrom());
        $this->assertEquals($taxData['zip_to'], $taxRateServiceData->getZipTo());
        $this->assertEquals('78765-78780', $taxRateServiceData->getTaxPostcode());
        $this->assertNotNull($taxRateServiceData->getId());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveWithZeroValue()
    {
        $taxData = [
            'tax_country_id' => 'US',
            'tax_region_id' => '8',
            'rate' => '0',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_is_range' => true,
            'zip_from' => 78765,
            'zip_to' => 78780,
        ];
        // Tax rate data object created
        $taxRate = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray($taxRate, $taxData, TaxRateInterface::class);
        //Tax rate service call
        $taxRateServiceData = $this->rateRepository->save($taxRate);

        //Assertions
        $this->assertInstanceOf(TaxRateInterface::class, $taxRateServiceData);
        $this->assertEquals($taxData['tax_country_id'], $taxRateServiceData->getTaxCountryId());
        $this->assertEquals($taxData['tax_region_id'], $taxRateServiceData->getTaxRegionId());
        $this->assertEquals($taxData['rate'], $taxRateServiceData->getRate());
        $this->assertEquals($taxData['code'], $taxRateServiceData->getCode());
        $this->assertEquals($taxData['zip_from'], $taxRateServiceData->getZipFrom());
        $this->assertEquals($taxData['zip_to'], $taxRateServiceData->getZipTo());
        $this->assertEquals('78765-78780', $taxRateServiceData->getTaxPostcode());
        $this->assertNotNull($taxRateServiceData->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Store/_files/store.php
     */
    public function testSaveWithTitles()
    {
        $store = $this->objectManager->get(Store::class);
        $store->load('test', 'code');

        $taxData = [
            'tax_country_id' => 'US',
            'tax_region_id' => '8',
            'rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_is_range' => true,
            'zip_from' => 78765,
            'zip_to' => 78780,
            'titles' => [
                [
                    'store_id' => $store->getId(),
                    'value' => 'random store title',
                ],
            ],
        ];
        // Tax rate data object created
        $taxRate = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray($taxRate, $taxData, TaxRateInterface::class);
        //Tax rate service call
        $taxRateServiceData = $this->rateRepository->save($taxRate);

        //Assertions
        $this->assertInstanceOf(TaxRateInterface::class, $taxRateServiceData);
        $this->assertEquals($taxData['tax_country_id'], $taxRateServiceData->getTaxCountryId());
        $this->assertEquals($taxData['tax_region_id'], $taxRateServiceData->getTaxRegionId());
        $this->assertEquals($taxData['rate'], $taxRateServiceData->getRate());
        $this->assertEquals($taxData['code'], $taxRateServiceData->getCode());
        $this->assertEquals($taxData['zip_from'], $taxRateServiceData->getZipFrom());
        $this->assertEquals($taxData['zip_to'], $taxRateServiceData->getZipTo());
        $this->assertEquals('78765-78780', $taxRateServiceData->getTaxPostcode());
        $this->assertNotNull($taxRateServiceData->getId());

        $titles = $taxRateServiceData->getTitles();
        $this->assertCount(1, $titles);
        $this->assertEquals($store->getId(), $titles[0]->getStoreId());
        $this->assertEquals($taxData['titles'][0]['value'], $titles[0]->getValue());

        $taxRateServiceData = $this->rateRepository->get($taxRateServiceData->getId());

        $titles = $taxRateServiceData->getTitles();
        $this->assertCount(1, $titles);
        $this->assertEquals($store->getId(), $titles[0]->getStoreId());
        $this->assertEquals($taxData['titles'][0]['value'], $titles[0]->getValue());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfTargetTaxRateDoesNotExist()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with taxRateId = 9999');

        $invalidTaxData = [
            'id' => 9999,
            'tax_country_id' => 'US',
            'tax_region_id' => '8',
            'rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_is_range' => true,
            'zip_from' => 78765,
            'zip_to' => 78780,
        ];
        $taxRate = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxRate,
            $invalidTaxData,
            TaxRateInterface::class
        );
        $this->rateRepository->save($taxRate);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfTaxRateWithCorrespondingCodeAlreadyExists()
    {
        $this->expectException(AlreadyExistsException::class);
        $this->expectExceptionMessage('Code already exists.');

        $invalidTaxData = [
            'tax_country_id' => 'US',
            'tax_region_id' => '8',
            'rate' => '8.25',
            'code' => 'US-CA-*-Rate' . rand(),
            'zip_is_range' => true,
            'zip_from' => 78765,
            'zip_to' => 78780,
        ];

        $taxRate1 = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxRate1,
            $invalidTaxData,
            TaxRateInterface::class
        );

        $taxRate2 = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxRate2,
            $invalidTaxData,
            TaxRateInterface::class
        );

        //Service call initiated twice to add the same code
        $this->rateRepository->save($taxRate1);
        $this->rateRepository->save($taxRate2);
    }

    /**
     * @param array $dataArray
     * @param string $errorMessages
     * @throws InputException
     *
     * @dataProvider createDataProvider
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfGivenDataIsInvalid($dataArray, $errorMessages)
    {
        $this->expectException(InputException::class);

        $taxRate = $this->taxRateFactory->create();
        $this->dataObjectHelper->populateWithArray($taxRate, $dataArray, TaxRateInterface::class);
        try {
            $this->rateRepository->save($taxRate);
        } catch (InputException $exception) {
            $errors = $exception->getErrors();
            foreach ($errors as $key => $error) {
                $this->assertEquals($errorMessages[$key], $error->getMessage());
            }
            throw $exception;
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createDataProvider()
    {
        return [
            'invalidZipRange' => [
                [
                    'zip_is_range' => true,
                    'zip_from' => 'from',
                    'zip_to' => 'to',
                ],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    'Invalid value of "from" provided for the zip_from field.',
                    'Invalid value of "to" provided for the zip_to field.',
                ],
            ],
            'emptyZipRange' => [
                [
                    'zip_is_range' => true,
                    'zip_from' => '',
                    'zip_to' => '',
                ],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    'Invalid value of "" provided for the zip_from field.',
                    'Invalid value of "" provided for the zip_to field.',
                ],
            ],
            'empty' => [
                [],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    '"postcode" is required. Enter and try again.',
                ],
            ],
            'zipRangeAndPostcode' => [
                [
                    'postcode' => 78727,
                    'zip_is_range' => true,
                    'zip_from' => 78765,
                    'zip_to' => 78780,
                ],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                ],
            ],
            'higherRange' => [
                [
                    'zip_is_range' => true,
                    'zip_from' => 78765,
                    'zip_to' => 78780,
                ],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    'Range To should be equal or greater than Range From.',
                ],
            ],
            'invalidCountry' => [
                ['tax_country_id' => 'XX'],
                'error' => [
                    'Invalid value of "XX" provided for the country_id field.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    '"postcode" is required. Enter and try again.',
                ],
            ],
            'invalidCountry2' => [
                ['tax_country_id' => ' '],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    '"postcode" is required. Enter and try again.',
                ],
            ],
            'invalidRegion1' => [
                ['tax_region_id' => '-'],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    'Invalid value of "-" provided for the region_id field.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    '"postcode" is required. Enter and try again.',
                ],
            ],
            'spaceRegion' => [
                ['tax_region_id' => ' '],
                'error' => [
                    '"country_id" is required. Enter and try again.',
                    '"percentage_rate" is required. Enter and try again.',
                    '"code" is required. Enter and try again.',
                    '"postcode" is required. Enter and try again.',
                ],
            ],
            'emptyPercentageRate' => [
                [
                    'tax_country_id' => 'US',
                    'tax_region_id' => '8',
                    'rate' => '',
                    'code' => 'US-CA-*-Rate' . rand(),
                    'zip_is_range' => true,
                    'zip_from' => 78765,
                    'zip_to' => 78780,
                ],
                'error' => [
                    '"percentage_rate" is required. Enter and try again.',
                ],
            ]

        ];
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGet()
    {
        $data = [
            'tax_country_id' => 'US',
            'tax_region_id' => '12',
            'tax_postcode' => '*',
            'code' => 'US_12_Code',
            'rate' => '7.5',
        ];
        $rate = $this->objectManager->create(Rate::class)
            ->setData($data)
            ->save();

        $taxRate = $this->rateRepository->get($rate->getId());

        $this->assertEquals('US', $taxRate->getTaxCountryId());
        $this->assertEquals(12, $taxRate->getTaxRegionId());
        $this->assertEquals('*', $taxRate->getTaxPostcode());
        $this->assertEquals('US_12_Code', $taxRate->getCode());
        $this->assertEquals(7.5, $taxRate->getRate());
        $this->assertNull($taxRate->getZipIsRange());
        $this->assertNull($taxRate->getZipTo());
        $this->assertNull($taxRate->getZipFrom());
    }

    /**
     */
    public function testGetThrowsExceptionIfTargetTaxRateDoesNotExist()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with taxRateId = 9999');

        $this->rateRepository->get(9999);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveUpdatesTaxRate()
    {
        $taxRate = $this->taxRateFactory->create();
        $taxRate->setTaxCountryId('US')
            ->setTaxRegionId(42)
            ->setRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setTaxPostcode('78780');
        $taxRate = $this->rateRepository->save($taxRate);

        $updatedTaxRate = $this->taxRateFactory->create();
        $updatedTaxRate->setId($taxRate->getId())
            ->setCode('UpdateTaxRates')
            ->setTaxCountryId('US')
            ->setTaxRegionId(42)
            ->setRate(8.25)
            ->setZipIsRange(true)
            ->setZipFrom(78700)
            ->setZipTo(78780);
        $updatedTaxRate = $this->rateRepository->save($updatedTaxRate);

        $retrievedRate = $this->rateRepository->get($taxRate->getId());
        // Expect the service to have filled in the new postcode for us
        $this->assertEquals($updatedTaxRate->getTaxPostcode(), $retrievedRate->getTaxPostcode());
        $this->assertNotEquals($taxRate->getTaxPostcode(), $retrievedRate->getTaxPostcode());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfTargetTaxRateExistsButProvidedDataIsInvalid()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('postcode');

        $taxRate = $this->taxRateFactory->create();
        $taxRate->setTaxCountryId('US')
            ->setTaxRegionId(42)
            ->setRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setTaxPostcode('78780');
        $taxRate = $this->rateRepository->save($taxRate);

        $updatedTaxRate = $this->taxRateFactory->create();
        $updatedTaxRate->setId($taxRate->getId())
            ->setTaxCountryId('US')
            ->setTaxRegionId(42)
            ->setRate(8.25)
            ->setCode('UpdateTaxRates')
            ->setTaxPostcode(null);
        $this->rateRepository->save($updatedTaxRate);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDeleteById()
    {
        // Create a new tax rate
        $taxRateData = $this->taxRateFactory->create();
        $taxRateData->setCode('TX')
            ->setTaxCountryId('US')
            ->setRate(5)
            ->setTaxPostcode(77000)
            ->setTaxRegionId(1);
        $taxRateId = $this->rateRepository->save($taxRateData)->getId();

        // Delete the new tax rate
        $this->assertTrue($this->rateRepository->deleteById($taxRateId));

        // Get the new tax rate, this should fail
        try {
            $this->rateRepository->get($taxRateId);
            $this->fail('NoSuchEntityException expected but not thrown');
        } catch (NoSuchEntityException $e) {
            $expectedParams = [
                'fieldName' => 'taxRateId',
                'fieldValue' => $taxRateId,
            ];
            $this->assertEquals($expectedParams, $e->getParameters());
        } catch (Exception $e) {
            $this->fail('Caught unexpected exception');
        }
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDeleteThrowsExceptionIfTargetTaxRateDoesNotExist()
    {
        // Create a new tax rate
        $taxRateData = $this->taxRateFactory->create();
        $taxRateData->setCode('TX')
            ->setTaxCountryId('US')
            ->setRate(6)
            ->setTaxPostcode(77001)
            ->setTaxRegionId(1);
        $taxRateId = $this->rateRepository->save($taxRateData)->getId();

        // Delete the new tax rate
        $this->assertTrue($this->rateRepository->deleteById($taxRateId));

        // Delete the new tax rate again, this should fail
        try {
            $this->rateRepository->deleteById($taxRateId);
            $this->fail('NoSuchEntityException expected but not thrown');
        } catch (NoSuchEntityException $e) {
            $expectedParams = [
                'fieldName' => 'taxRateId',
                'fieldValue' => $taxRateId,
            ];
            $this->assertEquals($expectedParams, $e->getParameters());
        } catch (Exception $e) {
            $this->fail('Caught unexpected exception');
        }
    }

    /**
     * @param Filter[] $filters
     * @param Filter[] $filterGroup
     * @param $expectedRateCodes
     *
     * @magentoDbIsolation enabled
     * @dataProvider searchTaxRatesDataProvider
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testGetList($filters, $filterGroup, $expectedRateCodes)
    {
        $taxRates = $this->taxRateFixtureFactory->createTaxRates(
            [
                ['percentage' => 7.5, 'country' => 'US', 'region' => '42'],
                ['percentage' => 7.5, 'country' => 'US', 'region' => '12'],
                ['percentage' => 22.0, 'country' => 'US', 'region' => '42'],
                ['percentage' => 10.0, 'country' => 'US', 'region' => '12'],
            ]
        );

        /** @var SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = Bootstrap::getObjectManager()
            ->create(SearchCriteriaBuilder::class);
        foreach ($filters as $filter) {
            $searchBuilder->addFilters([$filter]);
        }
        if ($filterGroup !== null) {
            $searchBuilder->addFilters($filterGroup);
        }
        $searchCriteria = $searchBuilder->create();

        $searchResults = $this->rateRepository->getList($searchCriteria);

        $this->assertEquals($searchCriteria, $searchResults->getSearchCriteria());
        $this->assertEquals(count($expectedRateCodes), $searchResults->getTotalCount());
        foreach ($searchResults->getItems() as $rate) {
            $this->assertContains($rate->getCode(), $expectedRateCodes);
        }
    }

    public function searchTaxRatesDataProvider()
    {
        $filterBuilder = Bootstrap::getObjectManager()->create(FilterBuilder::class);

        return [
            'eq' => [
                [$filterBuilder->setField(Rate::KEY_REGION_ID)->setValue(42)->create()],
                null,
                ['US - 42 - 7.5', 'US - 42 - 22'],
            ],
            'and' => [
                [
                    $filterBuilder->setField(Rate::KEY_REGION_ID)->setValue(42)->create(),
                    $filterBuilder->setField(Rate::KEY_PERCENTAGE_RATE)->setValue(22.0)->create(),
                ],
                [],
                ['US - 42 - 22'],
            ],
            'or' => [
                [],
                [
                    $filterBuilder->setField(Rate::KEY_PERCENTAGE_RATE)->setValue(22.0)->create(),
                    $filterBuilder->setField(Rate::KEY_PERCENTAGE_RATE)->setValue(10.0)->create(),
                ],
                ['US - 42 - 22', 'US - 12 - 10'],
            ],
            'like' => [
                [
                    $filterBuilder->setField(Rate::KEY_CODE)->setValue('%7.5')->setConditionType('like')->create(),
                ],
                [],
                ['US - 42 - 7.5', 'US - 12 - 7.5'],
            ],
            'like_region_name' => [
                [
                    $filterBuilder->setField(Rate::KEY_REGION_NAME)
                        ->setValue('%NM%')
                        ->setConditionType('like')
                        ->create(),
                ],
                null,
                ['US - 42 - 7.5', 'US - 42 - 22'],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->rateRepository = $this->objectManager->get(TaxRateRepositoryInterface::class);
        $this->taxRateFactory = $this->objectManager->create(TaxRateInterfaceFactory::class);
        $this->dataObjectHelper = $this->objectManager->create(DataObjectHelper::class);
        $this->taxRateFixtureFactory = new TaxRuleFixtureFactory();
        $this->countryFactory = $this->objectManager->create(CountryFactory::class);
        $this->regionFactory = $this->objectManager->create(RegionFactory::class);
    }
}
