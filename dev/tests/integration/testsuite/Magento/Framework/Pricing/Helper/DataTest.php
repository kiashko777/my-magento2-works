<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Pricing\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $helper;

    public function testCurrency()
    {
        $price = 10.00;
        $priceHtml = '<span class="price">$10.00</span>';
        $this->assertEquals($priceHtml, $this->helper->currency($price));
    }

    protected function setUp(): void
    {
        $this->helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}
