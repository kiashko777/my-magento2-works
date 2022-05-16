<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $_model;

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertIsArray($attributes);
        $this->assertNotEmpty($attributes);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Form::class
        );
        $this->_model->setFormCode('customer_account_create');
    }
}
