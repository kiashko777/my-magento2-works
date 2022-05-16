<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Layout\PageType\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class LayoutTest extends TestCase
{
    /**
     * @var Layout|MockObject
     */
    protected $_block;

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/page_types_select.html', $this->_block->toHtml());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $config = $this->getMockBuilder(
            Config::class
        )->setMethods(
            ['getPageTypes']
        )->disableOriginalConstructor()->getMock();
        $pageTypeValues = [
            'wishlist_index_index' => [
                'label' => 'Customer My Account My Wish List',
                'id' => 'wishlist_index_index',
            ],
            'cms_index_nocookies' => ['label' => 'CMS No-Cookies Page', 'id' => 'cms_index_nocookies'],
            'cms_index_defaultindex' => ['label' => 'CMS Home Default Page', 'id' => 'cms_index_defaultindex'],
        ];
        $config->expects($this->any())->method('getPageTypes')->willReturn($pageTypeValues);

        $this->_block = new Layout(
            $objectManager->get(Context::class),
            $config,
            [
                'name' => 'page_type',
                'id' => 'page_types_select',
                'class' => 'page-types-select',
                'title' => 'Page Types Select'
            ]
        );
    }
}
