<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module;

use Magento\Framework\DB\Adapter\TableNotFoundException;
use Magento\Framework\Setup\DataCacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class DataSetupTest extends TestCase
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $_model;

    public function testUpdateTableRow()
    {
        $original = $this->_model->getTableRow('setup_module', 'module', 'Magento_AdminNotification', 'schema_version');
        $this->_model->updateTableRow('setup_module', 'module', 'Magento_AdminNotification', 'schema_version', 'test');
        $this->assertEquals(
            'test',
            $this->_model->getTableRow('setup_module', 'module', 'Magento_AdminNotification', 'schema_version')
        );
        $this->_model->updateTableRow(
            'setup_module',
            'module',
            'Magento_AdminNotification',
            'schema_version',
            $original
        );
    }

    public function testDeleteTableRow()
    {
        $this->expectException(TableNotFoundException::class);

        $this->_model->deleteTableRow('setup/module', 'module', 'integration_test_fixture_setup');
    }

    /**
     * @covers \Magento\Setup\Module\DataSetup::updateTableRow
     */
    public function testUpdateTableRowNameConversion()
    {
        $this->expectException(TableNotFoundException::class);

        $original = $this->_model->getTableRow('setup_module', 'module', 'core_setup', 'schema_version');
        $this->_model->updateTableRow('setup/module', 'module', 'core_setup', 'schema_version', $original);
    }

    public function testTableExists()
    {
        $this->assertTrue($this->_model->tableExists('store_website'));
        $this->assertFalse($this->_model->tableExists('core/website'));
    }

    public function testGetSetupCache()
    {
        $this->assertInstanceOf(DataCacheInterface::class, $this->_model->getSetupCache());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            DataSetup::class
        );
    }
}
