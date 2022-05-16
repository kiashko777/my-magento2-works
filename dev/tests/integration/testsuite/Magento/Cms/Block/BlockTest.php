<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Block;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Cms/_files/block.php
     * @magentoDataFixture Magento/Variable/_files/variable.php
     * @magentoConfigFixture current_store web/unsecure/base_url http://example.com/
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://example.com/
     */
    public function testToHtml()
    {
        $cmsBlock = Bootstrap::getObjectManager()->create(
            \Magento\Cms\Model\Block::class
        );
        $cmsBlock->load('fixture_block', 'identifier');
        /** @var $block Block */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Block::class
        );
        $block->setBlockId($cmsBlock->getId());
        $result = $block->toHtml();
        $this->assertStringContainsString('<a href="http://example.com/', $result);
        $this->assertStringContainsString('<p>Config value: "http://example.com/".</p>', $result);
        $this->assertStringContainsString('<p>Custom variable: "HTML Value".</p>', $result);
    }
}
