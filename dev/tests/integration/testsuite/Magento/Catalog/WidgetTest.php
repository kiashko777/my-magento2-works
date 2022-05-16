<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog;

use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase
{
    public function testNewProductsWidget()
    {
        $type = NewWidget::class;

        /** @var $model Instance */
        $model = Bootstrap::getObjectManager()->create(
            Instance::class
        );
        $config = $model->setType($type)->getWidgetConfigAsArray();
        $templates = $config['parameters']['template']['values'];
        $this->assertArrayHasKey('default', $templates);
        $this->assertArrayHasKey('list', $templates);
        $this->assertArrayHasKey('list_default', $templates);
        $this->assertArrayHasKey('list_names', $templates);
        $this->assertArrayHasKey('list_images', $templates);

        $blocks = $config['supported_containers'];

        $containers = [];
        foreach ($blocks as $block) {
            $containers[] = $block['container_name'];
        }

        $this->assertContains('sidebar.main', $containers);
        $this->assertContains('content', $containers);
        $this->assertContains('sidebar.additional', $containers);

        // Verify that the correct id (code) is found for this widget instance type.
        $code = $model->setType($type)->getWidgetReference('type', $type, 'code');
        $this->assertEquals('new_products', $code);
    }
}
