<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab;

use Magento\Framework\Registry;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class GeneralTest extends TestCase
{
    /** @var LayoutInterface */
    protected $_layout;

    /** @var ThemeInterface */
    protected $_theme;

    /** @var General */
    protected $_block;

    public function testToHtmlPreviewImageNote()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_theme', $this->_theme);
        $this->_block->setArea('Adminhtml');

        $this->_block->toHtml();

        $noticeText = $this->_block->getForm()->getElement('preview_image')->getNote();
        $this->assertNotEmpty($noticeText);
    }

    public function testToHtmlPreviewImageUrl()
    {
        /** @var $objectManager ObjectManager */
        $this->_theme->setType(ThemeInterface::TYPE_PHYSICAL);
        $this->_theme->setPreviewImage('preview_image_test.jpg');
        $this->_block->setArea('Adminhtml');

        $html = $this->_block->toHtml();
        preg_match_all('/pub\/static\/Adminhtml\/_view\/en_US/', $html, $result);
        $this->assertEmpty($result[0]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $this->_theme = Bootstrap::getObjectManager()->create(
            ThemeInterface::class
        );
        $this->_theme->setType(ThemeInterface::TYPE_VIRTUAL);
        $this->_block = $this->_layout->createBlock(
            General::class
        );
    }
}
