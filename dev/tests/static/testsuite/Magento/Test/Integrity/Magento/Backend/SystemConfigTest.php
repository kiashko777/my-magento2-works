<?php
/**
 * Find "backend/system.xml" files and validate them
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Magento\Backend;

use DOMDocument;
use Magento\Framework\App\Utility\AggregateInvoker;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\UrnResolver;
use PHPUnit\Framework\TestCase;

class SystemConfigTest extends TestCase
{
    public function testSchema()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
        /**
         * @param string $configFile
         */
            function ($configFile) {
                $dom = new DOMDocument();
                $dom->loadXML(file_get_contents($configFile));
                $urnResolver = new UrnResolver();
                $schema = $urnResolver->getRealPath('urn:magento:module:Magento_Config:etc/system_file.xsd');
                $errors = Dom::validateDomDocument($dom, $schema);
                if ($errors) {
                    $this->fail('XML-file has validation errors:' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
                }
            },
            Files::init()->getConfigFiles('Adminhtml/system.xml', [])
        );
    }
}
