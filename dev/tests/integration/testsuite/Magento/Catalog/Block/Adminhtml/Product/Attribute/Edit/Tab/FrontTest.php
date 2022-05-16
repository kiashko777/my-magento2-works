<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class FrontTest extends TestCase
{
    /**
     * @var Front
     */
    private $block;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param $attributeCode
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($attributeCode)
    {
        /** @var Attribute $model */
        $model = $this->objectManager->create(Attribute::class);
        $model->loadByCode(Product::ENTITY, $attributeCode);

        /** @var Registry $coreRegistry */
        $coreRegistry = $this->objectManager->get(Registry::class);
        $coreRegistry->unregister('entity_attribute');
        $coreRegistry->register('entity_attribute', $model);

        $this->assertMatchesRegularExpression('/<select\sid="is_searchable".*disabled="disabled"/', $this->block->toHtml());
    }

    /**
     * @return array
     */
    public function toHtmlDataProvider()
    {
        return [
            ['visibility'],
            ['url_key'],
            ['status'],
            ['price_type'],
            ['category_ids'],
            ['media_gallery'],
            ['country_of_manufacture'],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var $layout Layout */
        $layout = $this->objectManager->create(LayoutInterface::class);
        $this->block = $layout->createBlock(Front::class);
    }
}
