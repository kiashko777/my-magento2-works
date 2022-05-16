<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Directory\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $helper;

    public function testGetDefaultCountry()
    {
        $this->assertEquals('US', $this->helper->getDefaultCountry());
    }

    protected function setUp(): void
    {
        $this->helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}
