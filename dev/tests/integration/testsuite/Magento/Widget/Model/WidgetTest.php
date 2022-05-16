<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase
{
    /**
     * @var Widget
     */
    protected $_model = null;

    public function testGetWidgetsArray()
    {
        $declaredWidgets = $this->_model->getWidgetsArray();
        $this->assertNotEmpty($declaredWidgets);
        $this->assertIsArray($declaredWidgets);
        foreach ($declaredWidgets as $row) {
            $this->assertArrayHasKey('name', $row);
            $this->assertArrayHasKey('code', $row);
            $this->assertArrayHasKey('type', $row);
            $this->assertArrayHasKey('description', $row);
        }
    }

    /**
     * @param string $type
     * @param string $expectedFile
     * @return string
     *
     * @dataProvider getPlaceholderImageUrlDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetPlaceholderImageUrl($type, $expectedFile)
    {
        $objectManager = Bootstrap::getObjectManager();
        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        $objectManager->get(DesignInterface::class)->setDesignTheme('Magento/backend');
        $expectedFilePath = "/Adminhtml/Magento/backend/en_US/{$expectedFile}";

        $url = $this->_model->getPlaceholderImageUrl($type);
        $this->assertStringEndsWith($expectedFilePath, $url);
    }

    /**
     * @return array
     */
    public function getPlaceholderImageUrlDataProvider()
    {
        return [
            'custom image' => [NewWidget::class,
                'Magento_Catalog/images/product_widget_new.png',
            ],
            'default image' => ['non_existing_widget_type', 'Magento_Widget/placeholder.png']
        ];
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Widget::class
        );
    }
}
