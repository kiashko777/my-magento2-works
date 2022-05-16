<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Config;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public function testConstruct()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root><key>value</key></root>
XML;
        $config = Bootstrap::getObjectManager()->create(
            Base::class,
            ['sourceData' => $xml]
        );

        $this->assertInstanceOf(Element::class, $config->getNode('key'));
    }
}
