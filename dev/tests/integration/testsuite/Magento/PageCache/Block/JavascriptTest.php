<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PageCache\Block;

use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\PageCache\Block\Javascript
 */
class JavascriptTest extends TestCase
{
    /**
     * @var Javascript
     */
    protected $javascript;

    /**
     * @var RequestInterface
     */
    protected $request;

    public function testGetScriptOptions()
    {
        $this->request->getQuery()->set('getparameter', 1);
        $this->assertStringContainsString('?getparameter=1', $this->javascript->getScriptOptions());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->request = $objectManager->get(RequestInterface::class);

        $this->javascript = $objectManager->create(
            Javascript::class
        );
    }
}
