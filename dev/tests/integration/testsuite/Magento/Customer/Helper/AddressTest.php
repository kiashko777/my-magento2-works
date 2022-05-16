<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /** @var Address */
    protected $helper;

    /**
     * @param $attributeCode
     * @dataProvider getAttributeValidationClass
     */
    public function testGetAttributeValidationClass($attributeCode, $expectedClass)
    {
        $this->assertEquals($expectedClass, $this->helper->getAttributeValidationClass($attributeCode));
    }

    public function getAttributeValidationClass()
    {
        return [
            ['bad-code', ''],
            ['city', 'required-entry'],
            ['company', ''],
            ['country_id', 'required-entry'],
            ['fax', ''],
            ['firstname', 'required-entry'],
            ['lastname', 'required-entry'],
            ['middlename', ''],
            ['postcode', '']
        ];
    }

    protected function setUp(): void
    {
        $this->helper = Bootstrap::getObjectManager()->get(
            Address::class
        );
    }
}
