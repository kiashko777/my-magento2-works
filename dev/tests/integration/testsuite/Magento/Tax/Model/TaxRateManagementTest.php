<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Tax\Api\TaxRateManagementInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TaxRateManagementTest extends TestCase
{
    /**
     * @var TaxRuleRepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @var TaxRuleFixtureFactory
     */
    private $taxRuleFixtureFactory;

    /**
     * Array of default tax classes ids
     *
     * Key is class name
     *
     * @var int[]
     */
    private $taxClasses;

    /**
     * Array of default tax rates ids.
     *
     * Key is rate percentage as string.
     *
     * @var int[]
     */
    private $taxRates;

    /**
     * Array of default tax rules ids.
     *
     * Key is rule code.
     *
     * @var int[]
     */
    private $taxRules;

    /**
     * @var TaxRateRepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @var TaxRateManagementInterface
     */
    private $taxRateManagement;

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetRatesByCustomerAndProductTaxClassId()
    {
        $this->setUpDefaultRules();
        $taxRateIds = $this->taxRuleRepository->get(current($this->taxRules))->getTaxRateIds();
        $expectedRates = [];
        foreach ($taxRateIds as $rateId) {
            $expectedRates[] = $this->taxRateRepository->get($rateId);
        }
        $rates = $this->taxRateManagement->getRatesByCustomerAndProductTaxClassId(
            $this->taxClasses['DefaultCustomerClass'],
            $this->taxClasses['DefaultProductClass']
        );

        $this->assertCount(2, $rates);
        $this->assertEquals($expectedRates, $rates);
    }

    private function setUpDefaultRules()
    {
        $this->taxRates = $this->taxRuleFixtureFactory->createTaxRates([
            ['percentage' => 7.5, 'country' => 'US', 'region' => 42],
            ['percentage' => 7.5, 'country' => 'US', 'region' => 12], // Default store rate
        ]);

        $this->taxClasses = $this->taxRuleFixtureFactory->createTaxClasses([
            ['name' => 'DefaultCustomerClass', 'type' => ClassModel::TAX_CLASS_TYPE_CUSTOMER],
            ['name' => 'DefaultProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
            ['name' => 'HigherProductClass', 'type' => ClassModel::TAX_CLASS_TYPE_PRODUCT],
        ]);

        $this->taxRules = $this->taxRuleFixtureFactory->createTaxRules([
            [
                'code' => 'Default Rule',
                'customer_tax_class_ids' => [$this->taxClasses['DefaultCustomerClass'], 3],
                'product_tax_class_ids' => [$this->taxClasses['DefaultProductClass']],
                'tax_rate_ids' => array_values($this->taxRates),
                'sort_order' => 0,
                'priority' => 0,
                'calculate_subtotal' => 1,
            ],
        ]);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->taxRuleRepository = $objectManager->get(TaxRuleRepositoryInterface::class);
        $this->taxRateManagement = $objectManager->get(TaxRateManagementInterface::class);
        $this->taxRateRepository = $objectManager->get(TaxRateRepositoryInterface::class);
        $this->taxRuleFixtureFactory = new TaxRuleFixtureFactory();
    }
}
