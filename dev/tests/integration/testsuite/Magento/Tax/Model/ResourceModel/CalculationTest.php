<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{
    /**
     * Test that Tax Rate applied only once
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetRate()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();

        $taxRule = $objectManager->get(Registry::class)
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getCustomerTaxClassIds();
        $productTaxClasses = $taxRule->getProductTaxClassIds();
        $taxRate = $objectManager->get(Registry::class)
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rate');
        $data = new DataObject();
        $data->setData(
            [
                'tax_country_id' => 'US',
                'taxregion_id' => '12',
                'tax_postcode' => '5555',
                'customer_class_id' => $customerTaxClasses[0],
                'product_class_id' => $productTaxClasses[0],
            ]
        );
        $taxCalculation = $objectManager->get(Calculation::class);
        $this->assertEquals($taxRate->getRateIds(), $taxCalculation->getRate($data));
    }
}
