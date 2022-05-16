<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Model\ResourceModel\Entity;

use Magento\Framework\Simplexml\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    /**
     * @var Table
     */
    protected $_model;

    public function testGetTable()
    {
        $this->assertEquals('test_table', $this->_model->getTable());
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->_model->getConfig());
        $this->assertEquals('test', $this->_model->getConfig('test_key'));
        $this->assertFalse($this->_model->getConfig('some_key'));
    }

    protected function setUp(): void
    {
        // @codingStandardsIgnoreStart
        $config = new Config();
        $config->table = 'test_table';
        $config->test_key = 'test';
        // @codingStandardsIgnoreEnd

        $this->_model = Bootstrap::getObjectManager()
            ->create(Table::class, ['config' => $config]);
    }
}
