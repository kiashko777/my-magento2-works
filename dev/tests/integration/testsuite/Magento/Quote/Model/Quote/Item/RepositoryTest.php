<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Model\Quote\Item;

use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Api\Data\UserInterface;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testGetList()
    {
        $expectedExtensionAttributes = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'admin@example.com'
        ];

        /** @var CartItemRepositoryInterface $quoteItemRepository */
        $quoteItemRepository = Bootstrap::getObjectManager()->create(
            CartItemRepositoryInterface::class
        );
        /** @var Quote $quote */
        $quote = Bootstrap::getObjectManager()->create(Quote::class);
        $quoteId = $quote->load('test01', 'reserved_order_id')->getId();

        /** @var CartItemInterface[] $quoteItems */
        $quoteItems = $quoteItemRepository->getList($quoteId);
        /** @var CartItemInterface $actualQuoteItem */
        $actualQuoteItem = array_pop($quoteItems);
        $this->assertInstanceOf(CartItemInterface::class, $actualQuoteItem);
        /** @var UserInterface $testAttribute */
        $testAttribute = $actualQuoteItem->getExtensionAttributes()->__toArray();
        $this->assertEquals(
            $expectedExtensionAttributes['firstname'],
            $testAttribute['quoteItemTestAttribute']['firstname']
        );
        $this->assertEquals(
            $expectedExtensionAttributes['lastname'],
            $testAttribute['quoteItemTestAttribute']['lastname']
        );
        $this->assertEquals(
            $expectedExtensionAttributes['email'],
            $testAttribute['quoteItemTestAttribute']['email']
        );
    }
}
