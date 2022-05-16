<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Backend\Block\Widget
 *
 * @magentoAppArea Adminhtml
 */
class WidgetTest extends TestCase
{
    /**
     * @covers \Magento\Backend\Block\Widget::getButtonHtml
     */
    public function testGetButtonHtml()
    {
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class,
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = $layout->createBlock(Widget::class);

        $this->assertMatchesRegularExpression(
            '/\<button.*\>[\s\S]*Button Label[\s\S]*<\/button>'
            . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/is',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );
    }

    /**
     * Case when two buttons will be created in same parent block
     *
     * @covers \Magento\Backend\Block\Widget::getButtonHtml
     */
    public function testGetButtonHtmlForTwoButtonsInOneBlock()
    {
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class,
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = $layout->createBlock(Widget::class);

        $this->assertMatchesRegularExpression(
            '/<button.*\>[\s\S]*Button Label[\s\S]*<\/button>'
            . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/ius',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );

        $this->assertMatchesRegularExpression(
            '/<button.*\>[\s\S]*Button Label2[\s\S]*<\/button>'
            . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/ius',
            $widget->getButtonHtml('Button Label2', 'this.form.submit()')
        );
    }

    public function testGetSuffixId()
    {
        $block = Bootstrap::getObjectManager()
            ->create(Widget::class);
        $this->assertStringEndsNotWith('_test', $block->getSuffixId('suffix'));
        $this->assertStringEndsWith('_test', $block->getSuffixId('test'));
    }
}
