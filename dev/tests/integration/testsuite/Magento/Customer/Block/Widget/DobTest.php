<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Customer\Block\Widget\Dob
 */

namespace Magento\Customer\Block\Widget;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DobTest extends TestCase
{
    public function testGetDateFormat()
    {
        $block = Bootstrap::getObjectManager()->create(
            Dob::class
        );
        $this->assertNotEmpty($block->getDateFormat());
    }
}
