<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular\Magento\Email;

use Exception;
use Magento\Email\Model\Template\Config;
use Magento\Email\Model\Template\Config\Reader;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EmailTemplateConfigFilesTest extends TestCase
{
    /**
     * Test that email template configuration file matches the format
     *
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        $urnResolver = new UrnResolver();
        $schemaFile = $urnResolver->getRealPath('urn:magento:module:Magento_Email:etc/email_templates.xsd');
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $dom = new Dom(file_get_contents($file), $validationStateMock);
        $result = $dom->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Files::init()->getConfigFiles('email_templates.xml');
    }

    /**
     * Test that email template configuration contains references to existing template files
     *
     * @param string $templateId
     * @dataProvider templateReferenceDataProvider
     */
    public function testTemplateReference($templateId)
    {
        /** @var Config $emailConfig */
        $emailConfig = Bootstrap::getObjectManager()->create(
            Config::class
        );

        $parts = $emailConfig->parseTemplateIdParts($templateId);
        $templateId = $parts['templateId'];

        $designParams = [];
        $theme = $parts['theme'];
        if ($theme) {
            $designParams['theme'] = $theme;
        }

        $templateFilename = $emailConfig->getTemplateFilename($templateId, $designParams);
        $this->assertFileExists($templateFilename, 'Email template file, specified in the configuration, must exist');
    }

    /**
     * @return array
     */
    public function templateReferenceDataProvider()
    {
        $data = [];
        /** @var Config $emailConfig */
        $emailConfig = Bootstrap::getObjectManager()->create(
            Config::class
        );
        foreach ($emailConfig->getAvailableTemplates() as $template) {
            $data[$template['value']] = [$template['value']];
        }
        return $data;
    }

    /**
     * Test that merged configuration of email templates matches the format
     */
    public function testMergedFormat()
    {
        $validationState = $this->createMock(ValidationStateInterface::class);
        $validationState->expects($this->any())->method('isValidationRequired')->willReturn(true);
        /** @var Reader $reader */
        $reader = Bootstrap::getObjectManager()->create(
            Reader::class,
            ['validationState' => $validationState]
        );
        try {
            $reader->read();
        } catch (Exception $e) {
            $this->fail('Merged email templates configuration does not pass XSD validation: ' . $e->getMessage());
        }
    }
}
