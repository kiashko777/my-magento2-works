<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Json\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $_helper = null;

    public function testJsonEncodeDecode()
    {
        $data = ['one' => 1, 'two' => 'two'];
        $jsonData = '{"one":1,"two":"two"}';
        $this->assertEquals($jsonData, $this->_helper->jsonEncode($data));
        $this->assertEquals($data, $this->_helper->jsonDecode($jsonData));
    }

    protected function setUp(): void
    {
        $this->_helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}
