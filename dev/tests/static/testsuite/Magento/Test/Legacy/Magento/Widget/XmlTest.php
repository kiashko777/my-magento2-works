<?php
/**
 * Test VS backwards-incompatible changes in widget.xml
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * A test for backwards-incompatible change in widget.xml structure
 */

namespace Magento\Test\Legacy\Magento\Widget;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class XmlTest extends TestCase
{
    public function testClassFactoryNames()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $xml = simplexml_load_file($file);
                $nodes = $xml->xpath('/widgets/*[@type]') ?: [];
                /** @var SimpleXMLElement $node */
                foreach ($nodes as $node) {
                    $type = (string)$node['type'];
                    $this->assertDoesNotMatchRegularExpression('/\//', $type, "Factory name detected: {$type}.");
                }
            },
            Files::init()->getConfigFiles('widget.xml')
        );
    }

    public function testBlocksIntoContainers()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $xml = simplexml_load_file($file);
                $this->assertSame(
                    [],
                    $xml->xpath('/widgets/*/supported_blocks'),
                    'Obsolete node: <supported_blocks>. To be replaced with <supported_containers>'
                );
                $this->assertSame(
                    [],
                    $xml->xpath('/widgets/*/*/*/block_name'),
                    'Obsolete node: <block_name>. To be replaced with <container_name>'
                );
            },
            Files::init()->getConfigFiles('widget.xml')
        );
    }
}
