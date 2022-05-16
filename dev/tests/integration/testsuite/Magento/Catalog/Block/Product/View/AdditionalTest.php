<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product\View;

use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AdditionalTest extends TestCase
{
    public function testGetChildHtmlList()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Additional */
        $block = $layout->createBlock(Additional::class, 'block');

        /** @var $childFirst Text */
        $childFirst = $layout->addBlock(Text::class, 'child1', 'block');
        $htmlFirst = '<b>Any html of child1</b>';
        $childFirst->setText($htmlFirst);

        /** @var $childSecond Text */
        $childSecond = $layout->addBlock(Text::class, 'child2', 'block');
        $htmlSecond = '<b>Any html of child2</b>';
        $childSecond->setText($htmlSecond);

        $list = $block->getChildHtmlList();

        $this->assertIsArray($list);
        $this->assertCount(2, $list);
        $this->assertContains($htmlFirst, $list);
        $this->assertContains($htmlSecond, $list);
    }
}
