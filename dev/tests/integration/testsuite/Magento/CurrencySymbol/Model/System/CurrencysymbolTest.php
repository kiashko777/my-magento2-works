<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CurrencySymbol\Model\System;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\CurrencySymbol\Model\System\Currencysymbol
 *
 * @magentoAppArea Adminhtml
 */
class CurrencysymbolTest extends TestCase
{
    /**
     * @var Currencysymbol
     */
    protected $currencySymbolModel;

    public function testGetCurrencySymbolsData()
    {
        $currencySymbolsData = $this->currencySymbolModel->getCurrencySymbolsData();
        $this->assertArrayHasKey('USD', $currencySymbolsData, 'Default currency option for USD is missing.');
        $this->assertArrayHasKey('EUR', $currencySymbolsData, 'Default currency option for EUR is missing.');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSetEmptyCurrencySymbolsData()
    {
        $currencySymbolsDataBefore = $this->currencySymbolModel->getCurrencySymbolsData();

        $this->currencySymbolModel->setCurrencySymbolsData([]);

        $currencySymbolsDataAfter = $this->currencySymbolModel->getCurrencySymbolsData();

        //Make sure symbol data is unchanged
        $this->assertEquals($currencySymbolsDataBefore, $currencySymbolsDataAfter);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSetCurrencySymbolsData()
    {
        $currencySymbolsData = $this->currencySymbolModel->getCurrencySymbolsData();
        $this->assertArrayHasKey('EUR', $currencySymbolsData);

        //Change currency symbol
        $currencySymbolsData = [
            'EUR' => '@',
        ];
        $this->currencySymbolModel->setCurrencySymbolsData($currencySymbolsData);

        //Verify if the new symbol is set
        $this->assertEquals(
            '@',
            $this->currencySymbolModel->getCurrencySymbolsData()['EUR']['displaySymbol'],
            'Symbol not set correctly.'
        );

        $this->assertEquals('@', $this->currencySymbolModel->getCurrencySymbol('EUR'), 'Symbol not set correctly.');
    }

    public function testGetCurrencySymbolNonExistent()
    {
        $this->assertFalse($this->currencySymbolModel->getCurrencySymbol('AUD'));
    }

    protected function setUp(): void
    {
        $this->currencySymbolModel = Bootstrap::getObjectManager()->create(
            Currencysymbol::class
        );
    }

    protected function tearDown(): void
    {
        $this->currencySymbolModel = null;
        Bootstrap::getObjectManager()->get(ReinitableConfigInterface::class)->reinit();
        Bootstrap::getObjectManager()->create(StoreManagerInterface::class)->reinitStores();
    }
}
