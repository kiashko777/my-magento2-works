<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests for obsolete and removed config nodes
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConfigFiles()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $file
         */
            function ($file) {
                $obsoleteNodes = [];
                $obsoleteNodesFiles = glob(__DIR__ . '/_files/obsolete_config_nodes*.php');
                foreach ($obsoleteNodesFiles as $obsoleteNodesFile) {
                    $obsoleteNodes = array_merge($obsoleteNodes, include $obsoleteNodesFile);
                }

                $xml = simplexml_load_file($file);
                foreach ($obsoleteNodes as $xpath => $suggestion) {
                    $this->assertEmpty(
                        $xml->xpath($xpath),
                        "Nodes identified by XPath '{$xpath}' are obsolete. {$suggestion}"
                    );
                }
            },
            Files::init()->getMainConfigFiles()
        );
    }
}
