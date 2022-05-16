<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $_model = null;

    public function testSetPriceIncludesTax()
    {
        $this->assertFalse($this->_model->priceIncludesTax());
        $this->assertSame($this->_model, $this->_model->setPriceIncludesTax(1));
        $this->assertTrue($this->_model->priceIncludesTax());
        $this->_model->setPriceIncludesTax(null);
        $this->assertFalse($this->_model->priceIncludesTax());
    }

    /**
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     */
    public function testPriceIncludesTaxNonDefault()
    {
        $this->assertTrue($this->_model->priceIncludesTax());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Config::class
        );
    }
}
