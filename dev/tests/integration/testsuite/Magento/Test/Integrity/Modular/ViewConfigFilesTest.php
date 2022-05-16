<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ViewConfigFilesTest extends TestCase
{
    /**
     * @param string $file
     * @dataProvider viewConfigFileDataProvider
     */
    public function testViewConfigFile($file)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $domConfig = new Dom($file, $validationStateMock);
        $urnResolver = new UrnResolver();
        $result = $domConfig->validate(
            $urnResolver->getRealPath('urn:magento:framework:Config/etc/view.xsd'),
            $errors
        );
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function viewConfigFileDataProvider()
    {
        $result = [];
        $files = Bootstrap::getObjectManager()->get(
            Reader::class
        )->getConfigurationFiles(
            'view.xml'
        );
        foreach ($files as $file) {
            $result[] = [$file];
        }
        return $result;
    }
}
