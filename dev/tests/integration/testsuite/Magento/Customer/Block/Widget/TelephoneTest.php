<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Widget;

use Magento\Customer\Model\Attribute;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Customer\Block\Widget\Taxvat
 *
 * @magentoAppArea frontend
 */
class TelephoneTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var Telephone $block */
        $block = Bootstrap::getObjectManager()->create(
            Telephone::class
        );

        $this->assertStringContainsString('title="Phone&#x20;Number"', $block->toHtml());
        $this->assertStringContainsString('required', $block->toHtml());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testToHtmlRequired()
    {
        /** @var Attribute $model */
        $model = Bootstrap::getObjectManager()->create(
            Attribute::class
        );
        $model->loadByCode('customer_address', 'telephone')->setIsRequired(false);
        $model->save();

        /** @var Telephone $block */
        $block = Bootstrap::getObjectManager()->create(
            Telephone::class
        );

        $this->assertStringContainsString('title="Phone&#x20;Number"', $block->toHtml());
        $this->assertStringNotContainsString('required', $block->toHtml());
    }

    protected function tearDown(): void
    {
        /** @var Config $eavConfig */
        $eavConfig = Bootstrap::getObjectManager()->get(Config::class);
        $eavConfig->clear();
    }
}
