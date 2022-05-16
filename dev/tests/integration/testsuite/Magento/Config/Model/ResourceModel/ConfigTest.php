<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Config\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $_model;

    public function testSaveDeleteConfig()
    {
        $connection = $this->_model->getConnection();
        $select = $connection->select()->from($this->_model->getMainTable())->where('path=?', 'test/config');
        $this->_model->saveConfig('test/config', 'test', 'default', 0);
        $this->assertNotEmpty($connection->fetchRow($select));

        $this->_model->deleteConfig('test/config', 'default', 0);
        $this->assertEmpty($connection->fetchRow($select));
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Config::class
        );
    }
}
