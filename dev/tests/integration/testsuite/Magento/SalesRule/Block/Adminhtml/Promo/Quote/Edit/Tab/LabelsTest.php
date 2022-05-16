<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Framework\View\Element\UiComponent\Argument\Interpreter\ConfigurableObject;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class LabelsTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            Labels::class,
            Bootstrap::getObjectManager()->get(
                ConfigurableObject::class
            )->evaluate(
                [
                    'name' => 'block',
                    'value' => Labels::class
                ]
            )
        );
    }
}
