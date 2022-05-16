<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Event\Config\SchemaLocator;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EventConfigFilesTest extends TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @param string $file
     * @dataProvider eventConfigFilesDataProvider
     */
    public function testEventConfigFiles($file)
    {
        $errors = [];
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $dom = new Dom(file_get_contents($file), $validationStateMock);
        $result = $dom->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function eventConfigFilesDataProvider()
    {
        return Files::init()->getConfigFiles('{*/events.xml,events.xml}');
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->_schemaFile = $objectManager->get(SchemaLocator::class)->getSchema();
    }
}
