<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Block;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testGetPage()
    {
        $page = Bootstrap::getObjectManager()->get(\Magento\Cms\Model\Page::class);
        $page->load('page100', 'identifier');
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $pageBlock = $layout->createBlock(Page::class);
        $pageBlock->setData('page', $page);
        $pageBlock->toHtml();
        $this->assertEquals($page, $pageBlock->getPage());
    }
}
