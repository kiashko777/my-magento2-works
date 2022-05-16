<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\TestFramework\Helper\Bootstrap as BootstrapHelper;
use PHPUnit\Framework\TestCase;

class MaskedQuoteIdToQuoteIdTest extends TestCase
{
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var GuestCartManagementInterface
     */
    private $guestCartManagement;

    public function testMaskedIdToQuoteId()
    {
        $maskedQuoteId = $this->guestCartManagement->createEmptyCart();
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($maskedQuoteId);

        self::assertGreaterThan(0, $quoteId);
    }

    public function testMaskedQuoteIdToQuoteIdForNonExistentQuote()
    {
        self::expectException(NoSuchEntityException::class);

        $this->maskedQuoteIdToQuoteId->execute('test');
    }

    protected function setUp(): void
    {
        $objectManager = BootstrapHelper::getObjectManager();
        $this->maskedQuoteIdToQuoteId = $objectManager->create(MaskedQuoteIdToQuoteIdInterface::class);
        $this->guestCartManagement = $objectManager->create(GuestCartManagementInterface::class);
    }
}
