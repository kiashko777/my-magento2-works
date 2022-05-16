<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\ValidationStateInterface;
use PHPUnit\Framework\TestCase;

class AclConfigFilesTest extends TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $_fileList = [];

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $_schemeFile;

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider aclConfigFileDataProvider
     */
    public function testAclConfigFile($file)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $domConfig = new Dom(file_get_contents($file), $validationStateMock);
        $result = $domConfig->validate($this->_schemeFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function aclConfigFileDataProvider()
    {
        return Files::init()->getConfigFiles('acl.xml');
    }

    protected function setUp(): void
    {
        $urnResolver = new UrnResolver();
        $this->_schemeFile = $urnResolver->getRealPath('urn:magento:framework:Acl/etc/acl.xsd');
    }
}
