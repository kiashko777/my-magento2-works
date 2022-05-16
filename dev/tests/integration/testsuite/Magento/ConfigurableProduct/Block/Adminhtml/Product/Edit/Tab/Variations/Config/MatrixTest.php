<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class MatrixTest extends AbstractBackendController
{
    const ATTRIBUTE_LABEL = 'New Attribute Label';
    const ATTRIBUTE_POSITION = 42;

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetVariations()
    {
        $this->_objectManager->get(
            Registry::class
        )->register(
            'current_product',
            Bootstrap::getObjectManager()->create(
                Product::class
            )->load(1)
        );
        Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Text::class,
            'head'
        );
        /** @var $usedAttribute Attribute */
        $usedAttribute = Bootstrap::getObjectManager()->get(
            Attribute::class
        )->loadByCode(
            Bootstrap::getObjectManager()->get(
                Config::class
            )->getEntityType(
                'catalog_product'
            )->getId(),
            'test_configurable'
        );
        $attributeOptions = $usedAttribute->getSource()->getAllOptions(false);
        /** @var $block Matrix */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            preg_replace('/Test$/', '', __CLASS__)
        );

        $variations = $block->getVariations();
        foreach ($variations as &$variation) {
            foreach ($variation as &$row) {
                unset($row['price']);
            }
        }

        $this->assertEquals(
            [
                [$usedAttribute->getId() => $attributeOptions[0]],
                [$usedAttribute->getId() => $attributeOptions[1]],
            ],
            $variations
        );
    }
}
