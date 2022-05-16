<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Options;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class AjaxTest extends TestCase
{
    /**
     * @var Ajax
     */
    protected $_block = null;

    public function testToHtmlWithoutProducts()
    {
        $this->assertEquals(json_encode([]), $this->_block->toHtml());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_with_options.php
     */
    public function testToHtml()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);

        $objectManager->get(Registry::class)
            ->register(
                'import_option_products',
                [$productRepository->get('simple')->getId()]
            );

        $result = json_decode($this->_block->toHtml(), true);

        $this->assertEquals('test_option_code_1', $result[0]['title']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Ajax::class
        );
    }
}
