<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product\ProductList;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use Magento\Catalog\ViewModel\Product\Listing\PreparePostData;
use Magento\Framework\App\Area;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Block\Products\List\Crosssell.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_crosssell.php
 */
class CrosssellTest extends TestCase
{
    public function testAll()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        Bootstrap::getInstance()->loadArea(Area::AREA_FRONTEND);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $objectManager->create(ProductRepositoryInterface::class);

        $firstProduct = $productRepository->get('simple');
        $product = $productRepository->get('simple_with_cross');

        $objectManager->get(Registry::class)->register('product', $product);
        /** @var $block Crosssell */
        $block = $objectManager->get(LayoutInterface::class)
            ->createBlock(Crosssell::class);
        $block->setLayout($objectManager->get(LayoutInterface::class));
        $block->setViewModel($objectManager->get(PreparePostData::class));
        $block->setTemplate('Magento_Catalog::product/list/items.phtml');
        $block->setType('crosssell');
        $block->setItemCount(1);
        $html = $block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Simple Cross Sell', $html);
        /* name */
        $this->assertStringContainsString('product/' . $firstProduct->getId() . '/', $html);
        /* part of url */
        $this->assertInstanceOf(
            Collection::class,
            $block->getItems()
        );
    }
}
