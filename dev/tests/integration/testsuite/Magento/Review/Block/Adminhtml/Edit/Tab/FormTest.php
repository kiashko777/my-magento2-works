<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Block\Adminhtml\Edit\Tab;

use Magento\Framework\View\LayoutInterface;
use Magento\Review\Block\Adminhtml\Rating\Edit\Tab\Form;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            Form::class,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Form::class
            )
        );
    }
}
