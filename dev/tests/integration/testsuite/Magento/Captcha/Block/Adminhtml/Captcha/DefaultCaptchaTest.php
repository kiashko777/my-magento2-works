<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Captcha\Block\Adminhtml\Captcha;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DefaultCaptchaTest extends TestCase
{
    /**
     * @var \Magento\Captcha\Block\Captcha\DefaultCaptcha
     */
    protected $_block;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testGetRefreshUrl()
    {
        $this->assertStringContainsString('backend/admin/refresh/refresh', $this->_block->getRefreshUrl());
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
