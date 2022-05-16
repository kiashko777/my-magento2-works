<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block\Catalog\Product;

use Magento\Backend\Block\Widget\Button;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Xpath;
use Magento\UrlRewrite\Block\Catalog\Category\Tree;
use Magento\UrlRewrite\Block\Catalog\Edit\Form;
use Magento\UrlRewrite\Block\Link;
use Magento\UrlRewrite\Block\Selector;
use Magento\UrlRewrite\Model\UrlRewrite;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        $block = $layout->createBlock(
            Edit::class,
            '',
            ['data' => $blockAttributes]
        );

        $this->_checkSelector($block, $expected);
        $this->_checkLinks($block, $expected);
        $this->_checkButtons($block, $expected);
        $this->_checkForm($block, $expected);
        $this->_checkGrid($block, $expected);
        $this->_checkCategories($block, $expected);
    }

    /**
     * Check selector
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkSelector($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $selectorBlock Selector|bool */
        $selectorBlock = $layout->getChildBlock($blockName, 'selector');

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
     * Check links
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkLinks($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $productLinkBlock Link|bool */
        $productLinkBlock = $layout->getChildBlock($blockName, 'product_link');

        if ($expected['product_link']) {
            $this->assertInstanceOf(
                Link::class,
                $productLinkBlock,
                'Child block with product link is invalid'
            );

            $this->assertEquals(
                'Products:',
                $productLinkBlock->getLabel(),
                'Child block with product link has invalid item label'
            );

            $this->assertEquals(
                $expected['product_link']['name'],
                $productLinkBlock->getItemName(),
                'Child block with product link has invalid item name'
            );

            $this->assertMatchesRegularExpression(
                '/http:\/\/localhost\/index.php\/.*\/product/',
                $productLinkBlock->getItemUrl(),
                'Child block with product link contains invalid URL'
            );
        } else {
            $this->assertFalse($productLinkBlock, 'Child block with product link should not present in block');
        }

        /** @var $categoryLinkBlock Link|bool */
        $categoryLinkBlock = $layout->getChildBlock($blockName, 'category_link');

        if ($expected['category_link']) {
            $this->assertInstanceOf(
                Link::class,
                $categoryLinkBlock,
                'Child block with category link is invalid'
            );

            $this->assertEquals(
                'Category:',
                $categoryLinkBlock->getLabel(),
                'Child block with category link has invalid item label'
            );

            $this->assertEquals(
                $expected['category_link']['name'],
                $categoryLinkBlock->getItemName(),
                'Child block with category link has invalid item name'
            );

            $this->assertMatchesRegularExpression(
                '/http:\/\/localhost\/index.php\/.*\/category/',
                $categoryLinkBlock->getItemUrl(),
                'Child block with category link contains invalid URL'
            );
        } else {
            $this->assertFalse($categoryLinkBlock, 'Child block with category link should not present in block');
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

        if (isset($expected['back_button'])) {
            if ($expected['back_button']) {
                if ($block->getProduct()->getId()) {
                    $this->assertRegExp(
                        '/setLocation\([\\\'\"]\S+?\/product/i',
                        $buttonsHtml,
                        'Back button is not present in category URL rewrite edit block'
                    );
                }
                $this->assertEquals(
                    1,
                    Xpath::getElementsCountForXpath(
                        '//button[contains(@class,"back")]',
                        $buttonsHtml
                    ),
                    'Back button is not present in product URL rewrite edit block'
                );
            } else {
                $this->assertEquals(
                    0,
                    Xpath::getElementsCountForXpath(
                        '//button[contains(@class,"back")]',
                        $buttonsHtml
                    ),
                    'Back button should not present in product URL rewrite edit block'
                );
            }
        }

        if ($expected['save_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"save")]',
                    $buttonsHtml
                ),
                'Save button is not present in product URL rewrite edit block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"save")]',
                    $buttonsHtml
                ),
                'Save button should not present in product URL rewrite edit block'
            );
        }

        if ($expected['reset_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[@title="Reset"]',
                    $buttonsHtml
                ),
                'Reset button is not present in product URL rewrite edit block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[@title="Reset"]',
                    $buttonsHtml
                ),
                'Reset button should not present in product URL rewrite edit block'
            );
        }

        if ($expected['delete_button']) {
            $this->assertEquals(
                1,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"delete")]',
                    $buttonsHtml
                ),
                'Delete button is not present in product URL rewrite edit block'
            );
        } else {
            $this->assertEquals(
                0,
                Xpath::getElementsCountForXpath(
                    '//button[contains(@class,"delete")]',
                    $buttonsHtml
                ),
                'Delete button should not present in product URL rewrite edit block'
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
                $block->getProduct(),
                $formBlock->getProduct(),
                'Form block should have same product attribute'
            );

            if ($block->getCategory()) {
                $this->assertSame(
                    $block->getCategory(),
                    $formBlock->getCategory(),
                    'Form block should have same category attribute'
                );
            }

            $this->assertSame(
                $block->getUrlRewrite(),
                $formBlock->getUrlRewrite(),
                'Form block should have same URL rewrite attribute'
            );
        } else {
            $this->assertFalse($formBlock, 'Child block with form should not present in block');
        }
    }

    /**
     * Check grid
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkGrid($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $gridBlock Grid|bool */
        $gridBlock = $layout->getChildBlock($blockName, 'products_grid');

        if ($expected['products_grid']) {
            $this->assertInstanceOf(
                Grid::class,
                $gridBlock,
                'Child block with product grid is invalid'
            );
        } else {
            $this->assertFalse($gridBlock, 'Child block with product grid should not present in block');
        }
    }

    /**
     * Check categories
     *
     * @param Edit $block
     * @param array $expected
     */
    private function _checkCategories($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $categoriesTreeBlock Tree|bool */
        $categoriesTreeBlock = $layout->getChildBlock($blockName, 'categories_tree');

        if ($expected['categories_tree']) {
            $this->assertInstanceOf(
                Tree::class,
                $categoriesTreeBlock,
                'Child block with categories tree is invalid'
            );
        } else {
            $this->assertFalse($categoriesTreeBlock, 'Child block with categories tree should not present in block');
        }

        /** @var $skipCategoriesBlock Button|bool */
        $skipCategoriesBlock = $layout->getChildBlock($blockName, 'skip_categories');

        if ($expected['skip_categories']) {
            $this->assertInstanceOf(
                Button::class,
                $skipCategoriesBlock,
                'Child block with skip categories is invalid'
            );
        } else {
            $this->assertFalse($skipCategoriesBlock, 'Child block with skip categories should not present in block');
        }
    }

    /**
     * Data provider
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function prepareLayoutDataProvider()
    {
        /** @var $urlRewrite UrlRewrite */
        $urlRewrite = Bootstrap::getObjectManager()->create(
            UrlRewrite::class
        );
        /** @var $product Product */
        $product = Bootstrap::getObjectManager()->create(
            Product::class,
            ['data' => ['entity_id' => 1, 'name' => 'Test product']]
        );
        /** @var $category Category */
        $category = Bootstrap::getObjectManager()->create(
            Category::class,
            ['data' => ['entity_id' => 1, 'name' => 'Test category']]
        );
        /** @var $existingUrlRewrite UrlRewrite */
        $existingUrlRewrite = Bootstrap::getObjectManager()->create(
            UrlRewrite::class,
            ['data' => ['url_rewrite_id' => 1]]
        );
        return [
            [ // Creating URL rewrite when product and category are not selected
                ['url_rewrite' => $urlRewrite],
                [
                    'selector' => true,
                    'product_link' => false,
                    'category_link' => false,
                    'back_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'save_button' => false,
                    'form' => false,
                    'products_grid' => true,
                    'categories_tree' => false,
                    'skip_categories' => false
                ],
            ],
            [ // Creating URL rewrite when product selected and category tree active
                ['product' => $product, 'url_rewrite' => $urlRewrite, 'is_category_mode' => true],
                [
                    'selector' => false,
                    'product_link' => ['name' => $product->getName()],
                    'category_link' => false,
                    'back_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'save_button' => false,
                    'form' => false,
                    'products_grid' => false,
                    'categories_tree' => true,
                    'skip_categories' => true
                ]
            ],
            [ // Creating URL rewrite when product selected and category tree inactive
                ['product' => $product, 'url_rewrite' => $urlRewrite],
                [
                    'selector' => false,
                    'product_link' => ['name' => $product->getName()],
                    'category_link' => false,
                    'back_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'save_button' => true,
                    'form' => true,
                    'products_grid' => false,
                    'categories_tree' => false,
                    'skip_categories' => false
                ]
            ],
            [ // Creating URL rewrite when product selected and category selected
                ['product' => $product, 'category' => $category, 'url_rewrite' => $urlRewrite],
                [
                    'selector' => false,
                    'product_link' => ['name' => $product->getName()],
                    'category_link' => ['name' => $category->getName()],
                    'back_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'save_button' => true,
                    'form' => true,
                    'products_grid' => false,
                    'categories_tree' => false,
                    'skip_categories' => false
                ]
            ],
            [ // Editing existing URL rewrite with product and category
                ['product' => $product, 'category' => $category, 'url_rewrite' => $existingUrlRewrite],
                [
                    'selector' => false,
                    'product_link' => ['name' => $product->getName()],
                    'category_link' => ['name' => $category->getName()],
                    'reset_button' => true,
                    'delete_button' => true,
                    'save_button' => true,
                    'form' => true,
                    'products_grid' => false,
                    'categories_tree' => false,
                    'skip_categories' => false
                ]
            ]
        ];
    }
}
