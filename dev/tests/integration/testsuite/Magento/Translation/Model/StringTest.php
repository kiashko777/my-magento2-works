<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Translation\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    /**
     * @var StringUtils
     */
    protected $_model;

    public function testConstructor()
    {
        $this->assertInstanceOf(
            \Magento\Translation\Model\ResourceModel\StringUtils::class,
            $this->_model->getResource()
        );
    }

    public function testSetGetString()
    {
        $expectedString = __METHOD__;
        $this->_model->setString($expectedString);
        $actualString = $this->_model->getString();
        $this->assertEquals($expectedString, $actualString);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            StringUtils::class
        );
    }
}
