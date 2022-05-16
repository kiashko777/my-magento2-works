<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Legacy tests to find obsolete system configuration declaration
 */

namespace Magento\Test\Legacy;

use Magento\Framework\App\Utility\Files;
use PHPUnit\Framework\TestCase;

class ObsoleteSystemConfigurationTest extends TestCase
{
    public function testSystemConfigurationDeclaration()
    {
        $fileList = Files::init()->getConfigFiles(
            'system.xml',
            ['wsdl.xml', 'wsdl2.xml', 'wsi.xml'],
            false
        );
        foreach ($fileList as $configFile) {
            $configXml = simplexml_load_file($configFile);
            $xpath = '/config/tabs|/config/sections';
            $this->assertEmpty(
                $configXml->xpath($xpath),
                'Obsolete system configuration structure detected in file ' . $configFile . '.'
            );
        }
    }
}
