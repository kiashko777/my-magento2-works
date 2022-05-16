<?php
/**
 * Integration test for Magento\Framework\Session\Config\Validator\CookiePathValidator
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Session\Config\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CookiePathValidatorTest extends TestCase
{
    /** @var  CookiePathValidator */
    private $model;

    public function testNoLeadingSlash()
    {
        $path = 'path';
        $this->assertFalse($this->model->isValid($path));
    }

    public function testInvalidPath()
    {
        $path = '/path?query=query';
        $this->assertFalse($this->model->isValid($path));
    }

    public function testValidPath()
    {
        $path = '/';
        $this->assertTrue($this->model->isValid($path));
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(CookiePathValidator::class);
    }
}
