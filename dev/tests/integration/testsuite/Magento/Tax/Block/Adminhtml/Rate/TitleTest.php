<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Block\Adminhtml\Rate;

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Tax\Controller\RegistryConstants;
use Magento\Tax\Model\Calculation\Rate;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    /**
     * @var Title
     */
    protected $_block;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @magentoDataFixture Magento/Store/_files/store.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetTitles()
    {
        /** @var Rate $rate */
        $rate = $this->_objectManager->create(Rate::class);
        $rate->load(1);
        /** @var Store $store */
        $store = $this->_objectManager->get(Store::class);
        $store->load('test', 'code');
        $title = 'title';
        $rate->saveTitles([$store->getId() => $title]);

        $coreRegistry = $this->_objectManager->create(Registry::class);
        $coreRegistry->register(RegistryConstants::CURRENT_TAX_RATE_ID, 1);

        /** @var Title $block */
        $block = Bootstrap::getObjectManager()->create(
            Title::class,
            [
                'coreRegistry' => $coreRegistry,
            ]
        );
        $titles = $block->getTitles();
        $this->assertArrayHasKey($store->getId(), $titles, 'Store was not created');
        $this->assertEquals($title, $titles[$store->getId()], 'Invalid Tax Title');
    }

    protected function setUp(): void
    {
        /** @var $objectManager ObjectManager */
        $this->_objectManager = Bootstrap::getObjectManager();
    }
}
