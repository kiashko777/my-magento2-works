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
class FaxTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var Fax $block */
        $block = Bootstrap::getObjectManager()->create(
            Fax::class
        );

        $this->assertStringContainsString('title="Fax"', $block->toHtml());
        $this->assertStringNotContainsString('required', $block->toHtml());
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
        $model->loadByCode('customer_address', 'fax')->setIsRequired(true);
        $model->save();

        /** @var Fax $block */
        $block = Bootstrap::getObjectManager()->create(
            Fax::class
        );

        $this->assertStringContainsString('title="Fax"', $block->toHtml());
        $this->assertStringContainsString('required', $block->toHtml());
    }

    protected function tearDown(): void
    {
        /** @var Config $eavConfig */
        $eavConfig = Bootstrap::getObjectManager()->get(Config::class);
        $eavConfig->clear();
    }
}
