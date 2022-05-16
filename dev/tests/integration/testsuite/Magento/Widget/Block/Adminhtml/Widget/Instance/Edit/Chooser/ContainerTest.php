<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $block = null;

    public function testSetGetAllowedContainers()
    {
        $this->assertEmpty($this->block->getAllowedContainers());
        $containers = ['some_container', 'another_container'];
        $this->block->setAllowedContainers($containers);
        $this->assertEquals($containers, $this->block->getAllowedContainers());
    }

    /**
     * Test verify that theme contains available containers for widget
     */
    public function testAvailableContainers()
    {
        $themeToTest = Bootstrap::getObjectManager()->get(
            Theme::class
        );
        $themeId = $themeToTest->load('Magento/blank', 'code')
            ->getId();
        $this->block->setTheme($themeId);
        $this->assertStringContainsString('<option value="before.body.end" >', $this->block->toHtml());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Container::class
        );
    }
}
