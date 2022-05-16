<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CurrencySymbol\Controller\Adminhtml\System\Currencysymbol;

use Magento\CurrencySymbol\Model\System\Currencysymbol;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

class SaveTest extends AbstractBackendController
{
    /**
     * Test save action.
     *
     * @param string $currencyCode
     * @param string $inputCurrencySymbol
     * @param string $outputCurrencySymbol
     *
     * @magentoConfigFixture               currency/options/allow USD
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @dataProvider currencySymbolDataProvider
     */
    public function testSaveAction($currencyCode, $inputCurrencySymbol, $outputCurrencySymbol)
    {
        /** @var Currencysymbol $currencySymbol */
        $currencySymbol = Bootstrap::getObjectManager()->create(
            Currencysymbol::class
        );

        $currencySymbolOriginal = $currencySymbol->getCurrencySymbol($currencyCode);

        $request = $this->getRequest();
        $request->setParam(
            'custom_currency_symbol',
            [
                $currencyCode => $inputCurrencySymbol,
            ]
        );
        $request->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('backend/admin/system_currencysymbol/save');

        $this->assertRedirect();

        $this->assertEquals(
            $outputCurrencySymbol,
            $currencySymbol->getCurrencySymbol($currencyCode),
            'Currency symbol has not been saved'
        );

        //restore current symbol
        $currencySymbol->setCurrencySymbolsData([$currencyCode => $currencySymbolOriginal]);
    }

    /**
     * @return array
     */
    public function currencySymbolDataProvider()
    {
        return [
            ['USD', 'customSymbolUSD', 'customSymbolUSD'],
            ['USD', '<script>customSymbolUSD</script>', 'customSymbolUSD']
        ];
    }
}
