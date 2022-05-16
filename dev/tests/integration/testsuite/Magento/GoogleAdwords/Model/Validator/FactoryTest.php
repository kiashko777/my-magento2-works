<?php
/**
 * Integration test for \Magento\GoogleAdwords\Model\Validator\Factory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GoogleAdwords\Model\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Test creation of conversion id validator
     *
     * @magentoAppIsolation enabled
     */
    public function testGetConversionIdValidator()
    {
        $conversionId = '123';

        $objectManager = Bootstrap::getObjectManager();
        $factory = $objectManager->get(Factory::class);

        $validator = $factory->createConversionIdValidator($conversionId);
        $this->assertNotNull($validator, "Conversion ID Validator");
    }

    /**
     * Test creation of conversion color validator
     *
     * @magentoAppIsolation enabled
     */
    public function testGetConversionColorValidator()
    {
        $conversionColor = "FFFFFF";

        $objectManager = Bootstrap::getObjectManager();
        $factory = $objectManager->get(Factory::class);

        $validator = $factory->createColorValidator($conversionColor);
        $this->assertNotNull($validator);
    }
}
