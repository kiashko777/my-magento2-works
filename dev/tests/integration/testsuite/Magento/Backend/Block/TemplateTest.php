<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Backend\Block\Template.
 *
 * @magentoAppArea Adminhtml
 */
class TemplateTest extends TestCase
{
    /**
     * @var Template
     */
    protected $_block;

    /**
     * @covers \Magento\Backend\Block\Template::getFormKey
     */
    public function testGetFormKey()
    {
        $this->assertGreaterThan(15, strlen($this->_block->getFormKey()));
    }

    /**
     * @magentoAppArea Adminhtml
     * @covers \Magento\Backend\Block\Template::isOutputEnabled
     * @magentoConfigFixture current_store advanced/modules_disable_output/dummy 1
     */
    public function testIsOutputEnabledTrue()
    {
        $this->_block->setData('module_name', 'dummy');
        $this->assertFalse($this->_block->isOutputEnabled('dummy'));
    }

    /**
     * @magentoAppArea Adminhtml
     * @covers \Magento\Backend\Block\Template::isOutputEnabled
     * @magentoConfigFixture current_store advanced/modules_disable_output/dummy 0
     */
    public function testIsOutputEnabledFalse()
    {
        $this->_block->setData('module_name', 'dummy');
        $this->assertTrue($this->_block->isOutputEnabled('dummy'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Template::class
        );
    }
}
