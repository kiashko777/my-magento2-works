<?php
/**
 * Integration test for Magento\Framework\Session\Config\Validator\CookieDomainValidator
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Session\Config\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CookieDomainValidatorTest extends TestCase
{
    /** @var  CookieDomainValidator */
    private $model;

    public function testEmptyString()
    {
        $this->assertTrue($this->model->isValid(''));
    }

    public function testInvalidHostname()
    {
        $this->assertFalse($this->model->isValid('http://'));
    }

    public function testNotString()
    {
        $this->assertFalse($this->model->isValid(1));
    }

    public function testNonemptyValid()
    {
        $this->assertTrue($this->model->isValid('domain.com'));
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(CookieDomainValidator::class);
    }
}
