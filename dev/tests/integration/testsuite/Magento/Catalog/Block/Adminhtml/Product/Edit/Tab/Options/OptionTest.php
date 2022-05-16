<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class OptionTest extends TestCase
{
    public function testGetOptionValuesCaching()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Option::class
        );
        /** @var $productWithOptions Product */
        $productWithOptions = Bootstrap::getObjectManager()->create(
            Product::class
        );
        $productWithOptions->setTypeId(
            'simple'
        )->setId(
            1
        )->setAttributeSetId(
            4
        )->setWebsiteIds(
            [1]
        )->setName(
            'Simple Products With Custom Options'
        )->setSku(
            'simple'
        )->setPrice(
            10
        )->setMetaTitle(
            'meta title'
        )->setMetaKeyword(
            'meta keyword'
        )->setMetaDescription(
            'meta description'
        )->setVisibility(
            Visibility::VISIBILITY_BOTH
        )->setStatus(
            Status::STATUS_ENABLED
        );

        $product = clone $productWithOptions;
        /** @var $option \Magento\Catalog\Model\Product\Option */
        $option = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Product\Option::class,
            ['data' => ['id' => 1, 'title' => 'some_title']]
        );
        $productWithOptions->setOptions([$option]);
        $block->setProduct($productWithOptions);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setProduct($product);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setIgnoreCaching(true);
        $this->assertEmpty($block->getOptionValues());
    }
}
