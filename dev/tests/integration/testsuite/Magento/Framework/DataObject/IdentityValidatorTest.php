<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DataObject;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class IdentityValidatorTest extends TestCase
{
    const VALID_UUID = 'fe563e12-cf9d-4faf-82cd-96e011b557b7';
    const INVALID_UUID = 'abcdef';

    /**
     * @var IdentityValidator
     */
    protected $identityValidator;

    public function testIsValid()
    {
        $isValid = $this->identityValidator->isValid(self::VALID_UUID);
        $this->assertTrue($isValid);
    }

    public function testIsNotValid()
    {
        $isValid = $this->identityValidator->isValid(self::INVALID_UUID);
        $this->assertFalse($isValid);
    }

    public function testEmptyValue()
    {
        $isValid = $this->identityValidator->isValid('');
        $this->assertFalse($isValid);
    }

    protected function setUp(): void
    {
        $this->identityValidator = Bootstrap::getObjectManager()
            ->get(IdentityValidator::class);
    }
}
