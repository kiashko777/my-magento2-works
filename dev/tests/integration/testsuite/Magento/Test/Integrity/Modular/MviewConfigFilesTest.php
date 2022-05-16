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

class MviewConfigFilesTest extends TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $fileList = [];

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $schemaFile;

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider mviewConfigFileDataProvider
     */
    public function testIndexerConfigFile($file)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $domConfig = new Dom(file_get_contents($file), $validationStateMock);
        $result = $domConfig->validate($this->schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function mviewConfigFileDataProvider()
    {
        return Files::init()->getConfigFiles('mview.xml');
    }

    protected function setUp(): void
    {
        $urnResolver = new UrnResolver();
        $this->schemaFile = $urnResolver->getRealPath('urn:magento:framework:Mview/etc/mview.xsd');
    }
}
