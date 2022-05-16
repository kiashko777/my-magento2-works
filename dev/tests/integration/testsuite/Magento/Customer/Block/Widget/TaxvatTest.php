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
class TaxvatTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        /** @var Taxvat $block */
        $block = Bootstrap::getObjectManager()->create(
            Taxvat::class
        );

        $model = Bootstrap::getObjectManager()->create(
            Attribute::class
        );
        $model->loadByCode('customer', 'taxvat');
        $attributeLabel = $model->getStoreLabel();

        $this->assertStringContainsString('title="' . $block->escapeHtmlAttr($attributeLabel) . '"', $block->toHtml());
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
        $model->loadByCode('customer', 'taxvat')->setIsRequired(true);
        $model->save();
        $attributeLabel = $model->getStoreLabel();

        /** @var Taxvat $block */
        $block = Bootstrap::getObjectManager()->create(
            Taxvat::class
        );

        $this->assertStringContainsString('title="' . $block->escapeHtmlAttr($attributeLabel) . '"', $block->toHtml());
        $this->assertStringContainsString('required', $block->toHtml());
    }

    protected function tearDown(): void
    {
        /** @var Config $eavConfig */
        $eavConfig = Bootstrap::getObjectManager()->get(Config::class);
        $eavConfig->clear();
    }
}
