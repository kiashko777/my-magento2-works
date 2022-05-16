<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Directory\Block;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $block;

    public function testGetCountryHtmlSelect()
    {
        $result = $this->block->getCountryHtmlSelect();
        $resultTwo = $this->block->getCountryHtmlSelect();
        $this->assertEquals($result, $resultTwo);
    }

    public function testGetRegionHtmlSelect()
    {
        $result = $this->block->getRegionHtmlSelect();
        $resultTwo = $this->block->getRegionHtmlSelect();
        $this->assertEquals($result, $resultTwo);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->block = $objectManager->get(Data::class);
    }
}
