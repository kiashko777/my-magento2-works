<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Block\Html;

use Magento\Customer\Model\Context;
use Magento\Framework\App\State;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme;
use PHPUnit\Framework\TestCase;

class FooterTest extends TestCase
{
    /**
     * @var Theme
     */
    protected $_theme;

    public function testGetCacheKeyInfo()
    {
        $objectManager = Bootstrap::getObjectManager();
        $context = $objectManager->get(\Magento\Framework\App\Http\Context::class);
        $context->setValue(Context::CONTEXT_AUTH, false, false);
        $block = $objectManager->get(LayoutInterface::class)
            ->createBlock(Footer::class);
        $storeId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();
        $this->assertEquals(
            ['PAGE_FOOTER', $storeId, 0, $this->_theme->getId(), false, $block->getTemplateFile(), 'template' => null],
            $block->getCacheKeyInfo()
        );
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode('frontend');
        $design = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        );
        $this->_theme = $design->setDefaultDesignTheme()->getDesignTheme();
    }
}
