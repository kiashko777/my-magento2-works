<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\EncryptionKey\Block\Adminhtml\Crypt\Key;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EditTest extends TestCase
{
    /**
     * Test edit block
     */
    public function testEditBlock()
    {
        /**
         * @var Edit
         */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Edit::class
        );

        $this->assertEquals('Encryption Key', $block->getHeaderText());
    }
}
