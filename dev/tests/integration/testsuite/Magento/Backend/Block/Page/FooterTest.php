<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Page;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test \Magento\Backend\Block\Page\Footer
 *
 * @magentoAppArea Adminhtml
 */
class FooterTest extends TestCase
{
    /**
     * Test Products Version Value
     */
    const TEST_PRODUCT_VERSION = '222.333.444';

    /**
     * @var Footer
     */
    protected $block;

    public function testToHtml()
    {
        $footerContent = $this->block->toHtml();
        $this->assertStringContainsString(
            'ver. ' . $this::TEST_PRODUCT_VERSION,
            $footerContent,
            'No or wrong product version.'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $productMetadataMock = $this->getMockBuilder(ProductMetadata::class)
            ->setMethods(['getVersion'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMetadataMock->expects($this->once())
            ->method('getVersion')
            ->willReturn($this::TEST_PRODUCT_VERSION);
        $this->block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Footer::class,
            '',
            ['productMetadata' => $productMetadataMock]
        );
    }
}
