<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Cron;

use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Sales\Cron\CleanExpiredQuotes class.
 *
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CleanExpiredQuotesTest extends TestCase
{
    /**
     * @var CleanExpiredQuotes
     */
    private $cleanExpiredQuotes;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * Check if outdated quotes are deleted.
     *
     * @magentoConfigFixture default_store checkout/cart/delete_quote_after -365
     * @magentoDataFixture Magento/Sales/_files/quotes.php
     */
    public function testExecute()
    {
        //Initial count - should be equal to stores number.
        $this->assertQuotesCount(2);

        //Deleting expired quotes
        $this->cleanExpiredQuotes->execute();

        //Only 1 will be deleted for the store that has all of them expired by config (default_store)
        $this->assertQuotesCount(1);
    }

    /**
     * Optimized assert quotes count
     * Uses collection getSize in order to get quick result
     *
     * @param int $expected
     */
    private function assertQuotesCount(int $expected): void
    {
        $totalCount = $this->quoteCollectionFactory->create()->getSize();
        $this->assertEquals($expected, $totalCount);
    }

    /**
     * Check if outdated quotes are deleted.
     *
     * @magentoConfigFixture default_store checkout/cart/delete_quote_after -365
     * @magentoDataFixture Magento/Sales/_files/quotes_big_amount.php
     */
    public function testExecuteWithBigAmountOfQuotes()
    {
        //Initial count - should be equal to 1000
        $this->assertQuotesCount(1000);

        //Deleting expired quotes
        $this->cleanExpiredQuotes->execute();

        //There should be no quotes anymore
        $this->assertQuotesCount(0);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->cleanExpiredQuotes = $objectManager->get(CleanExpiredQuotes::class);
        $this->quoteCollectionFactory = $objectManager->get(QuoteCollectionFactory::class);
    }
}
