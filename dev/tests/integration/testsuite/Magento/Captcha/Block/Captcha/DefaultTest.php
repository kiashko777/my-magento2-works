<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Captcha\Block\Captcha;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DefaultTest extends TestCase
{
    /**
     * @var DefaultCaptcha
     */
    protected $_block;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetRefreshUrlWhenFrontendStore()
    {
        $this->assertStringContainsString('captcha/refresh', $this->_block->getRefreshUrl());
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            DefaultCaptcha::class
        );
    }
}
