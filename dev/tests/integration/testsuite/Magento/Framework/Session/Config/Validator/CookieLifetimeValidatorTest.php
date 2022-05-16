<?php
/**
 * Integration test for  Magento\Framework\Session\Config\Validator\CookieLifetimeValidator
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Session\Config\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CookieLifetimeValidatorTest extends TestCase
{
    /** @var  CookieLifetimeValidator */
    private $model;

    public function testNonNumeric()
    {
        $this->assertFalse($this->model->isValid('non-numeric value'));
    }

    public function testNegative()
    {
        $this->assertFalse($this->model->isValid(-1));
    }

    public function testPositive()
    {
        $this->assertTrue($this->model->isValid(1));
    }

    public function testZero()
    {
        $this->assertTrue($this->model->isValid(0));
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            CookieLifetimeValidator::class
        );
    }
}
