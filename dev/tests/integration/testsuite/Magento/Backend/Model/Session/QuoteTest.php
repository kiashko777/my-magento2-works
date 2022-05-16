<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Session;

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class QuoteTest
 */
class QuoteTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetQuote()
    {
        /** Preconditions */
        $objectManager = Bootstrap::getObjectManager();
        $fixtureCustomerId = 1;
        /** @var Quote $backendQuoteSession */
        $backendQuoteSession = $objectManager->get(Quote::class);
        $backendQuoteSession->setCustomerId($fixtureCustomerId);
        /** @var Quote $quoteSession */
        $quoteSession = $objectManager->create(Quote::class);
        $quoteSession->setEntity(new DataObject());

        /** SUT execution */
        $quote = $quoteSession->getQuote();

        /** Ensure that customer data was added to quote correctly */
        $this->assertEquals(
            'John',
            $quote->getCustomer()->getFirstname(),
            'Customer data was set to quote incorrectly.'
        );
    }
}
