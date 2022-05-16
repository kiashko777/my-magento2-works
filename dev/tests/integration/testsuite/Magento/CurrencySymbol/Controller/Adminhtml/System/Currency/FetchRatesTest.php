<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CurrencySymbol\Controller\Adminhtml\System\Currency;

use Magento\Framework\Escaper;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test for fetchRates action
 */
class FetchRatesTest extends AbstractBackendController
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Test fetch action without service
     *
     * @return void
     */
    public function testFetchRatesActionWithoutService(): void
    {
        $request = $this->getRequest();
        $request->setParam(
            'rate_services',
            null
        );
        $this->dispatch('backend/admin/system_currency/fetchRates');

        $this->assertSessionMessages(
            $this->containsEqual('The Import Service is incorrect. Verify the service and try again.'),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * Test save action with nonexistent service
     *
     * @return void
     */
    public function testFetchRatesActionWithNonexistentService(): void
    {
        $request = $this->getRequest();
        $request->setParam(
            'rate_services',
            'non-existent-service'
        );
        $this->dispatch('backend/admin/system_currency/fetchRates');

        $this->assertSessionMessages(
            $this->containsEqual(
                $this->escaper->escapeHtml(
                    "The import model can't be initialized. Verify the model and try again."
                )
            ),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * Initial setup
     */
    protected function setUp(): void
    {
        $this->escaper = Bootstrap::getObjectManager()->create(
            Escaper::class
        );

        parent::setUp();
    }
}
