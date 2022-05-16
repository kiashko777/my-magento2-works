<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ResourceModel\Calculation\Rule\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TaxRuleCollectionTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCreateTaxRuleCollectionItem()
    {
        /** @var Collection $collection */
        $collection = Bootstrap::getObjectManager()->get(
            Collection::class
        );
        $dbTaxRulesQty = $collection->count();

        /** @var Rule $firstTaxRuleFixture */
        $firstTaxRuleFixture = Bootstrap::getObjectManager()->get(Registry::class)
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $expectedFirstTaxRuleId = $firstTaxRuleFixture->getId();

        if (($dbTaxRulesQty == 0) || ($collection->getFirstItem()->getId() != $expectedFirstTaxRuleId)) {
            $this->fail("Preconditions failed.");
        }
        /** @var TaxRuleCollection $taxRulesCollection */
        $taxRulesCollection = Bootstrap::getObjectManager()
            ->create(TaxRuleCollection::class);
        $collectionTaxRulesQty = $taxRulesCollection->count();
        $this->assertEquals($dbTaxRulesQty, $collectionTaxRulesQty, 'Tax rules quantity is invalid.');
        $taxRule = $taxRulesCollection->getFirstItem()->getData();
        $expectedTaxRuleData = [
            'tax_calculation_rule_id' => $expectedFirstTaxRuleId,
            'code' => 'Test Rule',
            'priority' => '0',
            'position' => '0',
            'calculate_subtotal' => '0',
            'customer_tax_classes' => $firstTaxRuleFixture->getCustomerTaxClassIds(),
            'product_tax_classes' => $firstTaxRuleFixture->getProductTaxClassIds(),
            'tax_rates' => $firstTaxRuleFixture->getTaxRateIds(),
            'tax_rates_codes' => $firstTaxRuleFixture->getTaxRatesCodes()
        ];

        $this->assertEquals($expectedTaxRuleData, $taxRule, 'Tax rule data is invalid.');
    }
}
