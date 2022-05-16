<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class to verify isIncrementIdUsed method behaviour.
 */
class OrderIncrementIdCheckerTest extends TestCase
{
    /**
     * @var OrderIncrementIdChecker
     */
    private $checker;

    /**
     * Test to verify if isIncrementIdUsed method works with numeric increment ids.
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @return void
     */
    public function testIsOrderIncrementIdUsedNumericIncrementId(): void
    {
        $this->assertTrue($this->checker->isIncrementIdUsed('100000001'));
    }

    /**
     * Test to verify if isIncrementIdUsed method works with alphanumeric increment ids.
     *
     * @magentoDataFixture Magento/Sales/_files/order_alphanumeric_id.php
     * @return void
     */
    public function testIsOrderIncrementIdUsedAlphanumericIncrementId(): void
    {
        $this->assertTrue($this->checker->isIncrementIdUsed('M00000001'));
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->checker = Bootstrap::getObjectManager()->create(
            OrderIncrementIdChecker::class
        );
    }
}
