<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular\Magento\Catalog;

use Magento\Catalog\Model\Attribute\Config\SchemaLocator;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AttributeConfigFilesTest extends TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $dom = new Dom(file_get_contents($file), $validationStateMock);
        $result = $dom->validate($this->_schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Files::init()->getConfigFiles('catalog_attributes.xml');
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var SchemaLocator $schemaLocator */
        $schemaLocator = $objectManager->get(SchemaLocator::class);
        $this->_schemaFile = $schemaLocator->getSchema();
    }
}
