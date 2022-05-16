<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Paypal\Block\Express\Review
 */

namespace Magento\Paypal\Block\Express;

use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testRenderAddress()
    {
        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quote->load('test01', 'reserved_order_id');

        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Review::class
        );
        $addressData = include __DIR__ . '/../../../Sales/_files/address_data.php';
        $address = Bootstrap::getObjectManager()->create(
            Address::class,
            ['data' => $addressData]
        );
        $address->setAddressType('billing');
        $address->setQuote($quote);
        $this->assertStringContainsString('Los Angeles', $block->renderAddress($address));
    }
}
