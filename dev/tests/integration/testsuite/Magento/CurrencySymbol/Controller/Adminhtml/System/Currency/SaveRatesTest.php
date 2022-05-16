<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CurrencySymbol\Controller\Adminhtml\System\Currency;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Escaper;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

class SaveRatesTest extends AbstractBackendController
{

    /** @var Currency $currencyRate */
    protected $currencyRate;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Test save action
     *
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $currencyCode = 'USD';
        $currencyTo = 'USD';
        $rate = 1.0000;

        $request = $this->getRequest();
        $request->setMethod(HttpRequest::METHOD_POST);
        $request->setPostValue(
            'rate',
            [
                $currencyCode => [$currencyTo => $rate]
            ]
        );
        $this->dispatch('backend/admin/system_currency/saveRates');

        $this->assertSessionMessages(
            $this->containsEqual((string)__('All valid rates have been saved.')),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertEquals(
            $rate,
            $this->currencyRate->load($currencyCode)->getRate($currencyTo),
            'Currency rate has not been saved'
        );
    }

    /**
     * Test save action with warning
     *
     * @magentoDbIsolation enabled
     */
    public function testSaveWithWarningAction()
    {
        $currencyCode = 'USD';
        $currencyTo = 'USD';
        $rate = '0';

        $request = $this->getRequest();
        $request->setMethod(HttpRequest::METHOD_POST);
        $request->setPostValue(
            'rate',
            [
                $currencyCode => [$currencyTo => $rate]
            ]
        );
        $this->dispatch('backend/admin/system_currency/saveRates');

        $this->assertSessionMessages(
            $this->containsEqual(
                $this->escaper->escapeHtml(
                    (string)__('Please correct the input data for "%1 => %2" rate.', $currencyCode, $currencyTo)
                )
            ),
            MessageInterface::TYPE_WARNING
        );
    }

    /**
     * Initial setup
     */
    protected function setUp(): void
    {
        $this->currencyRate = Bootstrap::getObjectManager()->create(
            Currency::class
        );
        $this->escaper = Bootstrap::getObjectManager()->create(
            Escaper::class
        );

        parent::setUp();
    }
}
