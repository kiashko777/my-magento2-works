<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class LinksTest
 *
 * @package Magento\Downloadable\Block\Adminhtml\Catalog\Products\Edit\Tab\Downloadable
 * @deprecated
 * @see \Magento\Downloadable\Ui\DataProvider\Products\Form\Modifier\Links
 */
class LinksTest extends TestCase
{
    /**
     * @magentoAppArea Adminhtml
     */
    public function testGetUploadButtonsHtml()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Links::class
        );
        self::performUploadButtonTest($block);
    }

    /**
     * Reuse code for testing getUploadButtonHtml()
     *
     * @param AbstractBlock $block
     */
    public static function performUploadButtonTest(AbstractBlock $block)
    {
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class
        );
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Text::class,
            '',
            ['data' => ['text' => $expected]]
        );
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoAppIsolation enabled
     */
    public function testGetLinkData()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            Registry::class
        )->register(
            'product',
            new DataObject(['type_id' => 'simple'])
        );
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Links::class
        );
        $this->assertEmpty($block->getLinkData());
    }

    /**
     * Get Links Title for simple/virtual/downloadable product
     *
     * @magentoConfigFixture current_store catalog/downloadable/links_title Links Title Test
     * @magentoAppIsolation enabled
     * @dataProvider productLinksTitleDataProvider
     *
     * @magentoAppArea Adminhtml
     * @param string $productType
     * @param string $linksTitle
     * @param string $expectedResult
     */
    public function testGetLinksTitle($productType, $linksTitle, $expectedResult)
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            Registry::class
        )->register(
            'product',
            new DataObject(['type_id' => $productType, 'id' => '1', 'links_title' => $linksTitle])
        );
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Links::class
        );
        $this->assertEquals($expectedResult, $block->getLinksTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productLinksTitleDataProvider()
    {
        return [
            ['simple', null, 'Links Title Test'],
            ['simple', 'Links Title', 'Links Title Test'],
            ['virtual', null, 'Links Title Test'],
            ['virtual', 'Links Title', 'Links Title Test'],
            ['downloadable', null, null],
            ['downloadable', 'Links Title', 'Links Title']
        ];
    }
}
