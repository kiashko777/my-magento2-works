<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Page;

use Magento\Backend\Helper\Data;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test \Magento\Backend\Block\Page\Header
 * @magentoAppArea Adminhtml
 */
class HeaderTest extends TestCase
{
    /**
     * @var Header
     */
    protected $_block;

    public function testGetHomeLink()
    {
        $expected = Bootstrap::getObjectManager()->get(
            Data::class
        )->getHomePageUrl();
        $this->assertEquals($expected, $this->_block->getHomeLink());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Header::class
        );
    }
}
