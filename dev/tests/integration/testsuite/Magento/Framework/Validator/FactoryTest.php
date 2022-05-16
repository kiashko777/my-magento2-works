<?php
/**
 * Integration test for \Magento\Framework\Validator\Factory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Validator;

use Magento\Framework\Phrase;
use Magento\Framework\Translate\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Test creation of validator config
     *
     * @magentoAppIsolation enabled
     */
    public function testGetValidatorConfig()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Factory $factory */
        $factory = $objectManager->get(Factory::class);
        $this->assertInstanceOf(Config::class, $factory->getValidatorConfig());
        // Check that default translator was set
        $translator = AbstractValidator::getDefaultTranslator();
        $this->assertInstanceOf(AdapterInterface::class, $translator);
        $this->assertEquals('Message', new Phrase('Message'));
        $this->assertEquals('Message', $translator->translate('Message'));
        $this->assertEquals(
            'Message with "placeholder one" and "placeholder two"',
            (string)new Phrase('Message with "%1" and "%2"', ['placeholder one', 'placeholder two'])
        );
    }
}
