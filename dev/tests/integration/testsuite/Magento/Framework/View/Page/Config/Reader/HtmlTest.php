<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Page\Config\Reader;

use Magento\Framework\View\Layout\Element;
use Magento\Framework\View\Layout\Reader\Context;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testInterpret()
    {
        /** @var Context $readerContext */
        $readerContext = Bootstrap::getObjectManager()->create(
            Context::class
        );
        $pageXml = new Element(__DIR__ . '/_files/_layout_update.xml', 0, true);
        $parentElement = new Element('<page></page>');

        $html = new Html();
        foreach ($pageXml->xpath('html') as $htmlElement) {
            $html->interpret($readerContext, $htmlElement, $parentElement);
        }

        $structure = $readerContext->getPageConfigStructure();
        $this->assertEquals(['html' => ['test-name' => 'test-value']], $structure->getElementAttributes());
    }
}
