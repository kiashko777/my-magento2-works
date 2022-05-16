<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Pricing;

use Magento\Tax\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AdjustmentTest extends TestCase
{
    /**
     * @param bool $isShippingPriceExcludeTax
     * @param bool $expectedResult
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     * @dataProvider isIncludedInBasePricePriceIncludeTaxEnabledDataProvider
     */
    public function testIsIncludedInBasePricePriceIncludeTacEnabled($isShippingPriceExcludeTax, $expectedResult)
    {
        $this->isIncludedInBasePricePrice($isShippingPriceExcludeTax, $expectedResult);
    }

    /**
     * @param $isShippingPriceExcludeTax
     * @param $expectedResult
     */
    protected function isIncludedInBasePricePrice($isShippingPriceExcludeTax, $expectedResult)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Config $config */
        $config = $objectManager->get(Config::class);
        /** @var Adjustment $model */
        $model = $objectManager->create(Adjustment::class);
        $config->setNeedUseShippingExcludeTax($isShippingPriceExcludeTax);
        // Run tested method
        $result = $model->isIncludedInBasePrice();
        // Check expectations
        $this->assertIsBool($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @param bool $isShippingPriceExcludeTax
     * @param bool $expectedResult
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @dataProvider isIncludedInBasePricePriceIncludeTaxDisabledDataProvider
     */
    public function testIsIncludedInBasePricePriceIncludeTacDisabled($isShippingPriceExcludeTax, $expectedResult)
    {
        $this->isIncludedInBasePricePrice($isShippingPriceExcludeTax, $expectedResult);
    }

    /**
     * @return array
     */
    public function isIncludedInBasePricePriceIncludeTaxEnabledDataProvider()
    {
        return [
            [0, true],
            [1, true],
        ];
    }

    /**
     * @return array
     */
    public function isIncludedInBasePricePriceIncludeTaxDisabledDataProvider()
    {
        return [
            [0, false],
            [1, true],
        ];
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store tax/display/type 1
     */
    public function testIsIncludedInDisplayPriceExcludingTax()
    {
        $this->isIncludedInDisplayPrice(false);
    }

    /**
     * test template for isIncludedInDisplayPrice
     *
     * @param $expectedResult
     */
    protected function isIncludedInDisplayPrice($expectedResult)
    {
        // Instantiate objects
        $objectManager = Bootstrap::getObjectManager();
        /** @var Adjustment $model */
        $model = $objectManager->create(Adjustment::class);
        // Run tested method
        $result = $model->isIncludedInDisplayPrice();
        // Check expectations
        $this->assertIsBool($result);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store tax/display/type 2
     */
    public function testIsIncludedInDisplayPriceIncludingTax()
    {
        $this->isIncludedInDisplayPrice(true);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store tax/display/type 3
     */
    public function testIsIncludedInDisplayPriceBoth()
    {
        $this->isIncludedInDisplayPrice(true);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store tax/display/type 100500
     */
    public function testIsIncludedInDisplayPriceWrongValue()
    {
        $this->isIncludedInDisplayPrice(false);
    }
}
