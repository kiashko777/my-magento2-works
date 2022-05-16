<?php
/**
 * Integration test for Magento\Framework\ValidatorFactory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ValidatorFactoryTest extends TestCase
{
    /** @var  ValidatorFactory */
    private $model;

    public function testCreateWithInstanceName()
    {
        $setName = DataObject::class;
        $this->assertInstanceOf($setName, $this->model->create([], $setName));
    }

    public function testCreateDefault()
    {
        $default = Validator::class;
        $this->assertInstanceOf($default, $this->model->create());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(ValidatorFactory::class);
    }
}
