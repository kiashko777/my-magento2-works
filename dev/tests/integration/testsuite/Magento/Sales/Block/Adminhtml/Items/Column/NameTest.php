<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Block\Adminhtml\Items\Column;

use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Items\AbstractItems;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class NameTest extends TestCase
{
    /**
     * @var Name
     */
    private $block;

    public function testTruncateString(): void
    {
        $remainder = '';
        $this->assertEquals(
            '12345',
            $this->block->truncateString('1234567890', 5, '', $remainder)
        );
    }

    public function testGetFormattedOptiong(): void
    {
        $this->assertEquals(
            [
                'value' => '1234567890123456789012345678901234567890123456789012345',
                'remainder' => '67890',
            ],
            $this->block->getFormattedOption(
                '123456789012345678901234567890123456789012345678901234567890'
            )
        );
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $layout Layout */
        $layout = $objectManager->create(LayoutInterface::class);
        /** @var $block AbstractItems */
        $this->block = $layout->createBlock(Name::class, 'block');
    }
}
