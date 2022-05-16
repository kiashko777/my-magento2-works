<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Xpath;
use Magento\UrlRewrite\Block\Edit\Form;
use Magento\UrlRewrite\Model\UrlRewrite;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\UrlRewrite\Block\Edit
 * @magentoAppArea Adminhtml
 */
class EditTest extends TestCase
{
    /**
     * Test prepare layout
     *
     * @dataProvider prepareLayoutDataProvider
     *
     * @param array $blockAttributes
     * @param array $expected
     *
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout($blockAttributes, $expected)
    {
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );

        /** @var $block Edit */
        $block = $layout->createBlock(Edit::class, '', ['data' => $blockAttributes]);

        $this->_checkSelector($block, $expected);
        $this->_checkButtons($block, $expected);
        $this->_checkForm($block, $expected);
    }

    /**
     * Check entity selector
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkSelector($block, $expected)
    {
        $layout = $block->getLayout();

        /** @var $selectorBlock Selector|bool */
        $selectorBlock = $layout->getChildBlock($block->getNameInLayout(), 'selector');

        if ($expected['selector']) {
            $this->assertInstanceOf(
                Selector::class,
                $selectorBlock,
                'Child block with entity selector is invalid'
            );
        } else {
            $this->assertFalse($selectorBlock, 'Child block with entity selector should not present in block');
        }
    }

    /**
     * Check buttons
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkButtons($block, $expected)
    {
        $buttonsHtml = $block->getButtonsHtml();

        if ($expected['back_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"back")]',
                    $buttonsHtml
                ),
                'Back button is not present in block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"back")]',
                    $buttonsHtml
                ),
                'Back button should not present in block'
            );
        }

        if ($expected['save_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"save")]',
                    $buttonsHtml
                ),
                'Save button is not present in block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"save")]',
                    $buttonsHtml
                ),
                'Save button should not present in block'
            );
        }

        if ($expected['reset_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[@title="Reset"]',
                    $buttonsHtml
                ),
                'Reset button is not present in block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[@title="Reset"]',
                    $buttonsHtml
                ),
                'Reset button should not present in block'
            );
        }

        if ($expected['delete_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"delete")]',
                    $buttonsHtml
                ),
                'Delete button is not present in block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"delete")]',
                    $buttonsHtml
                ),
                'Delete button should not present in block'
            );
        }
    }

    /**
     * Check form
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkForm($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $formBlock Form|bool */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        if ($expected['form']) {
            $this->assertInstanceOf(
                Form::class,
                $formBlock,
                'Child block with form is invalid'
            );

            $this->assertSame(
                $expected['form']['url_rewrite'],
                $formBlock->getUrlRewrite(),
                'Form block should have same URL rewrite attribute'
            );
        } else {
            $this->assertFalse($formBlock, 'Child block with form should not present in block');
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function prepareLayoutDataProvider()
    {
        /** @var $urlRewrite UrlRewrite */
        $urlRewrite = Bootstrap::getObjectManager()->create(
            UrlRewrite::class
        );
        /** @var $existingUrlRewrite UrlRewrite */
        $existingUrlRewrite = Bootstrap::getObjectManager()->create(
            UrlRewrite::class,
            ['data' => ['url_rewrite_id' => 1]]
        );

        return [
            // Creating new URL rewrite
            [
                ['url_rewrite' => $urlRewrite],
                [
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'form' => ['url_rewrite' => $urlRewrite]
                ]
            ],
            // Editing URL rewrite
            [
                ['url_rewrite' => $existingUrlRewrite],
                [
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => true,
                    'delete_button' => true,
                    'form' => ['url_rewrite' => $existingUrlRewrite]
                ]
            ]
        ];
    }
}
