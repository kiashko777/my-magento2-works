<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestSetupDeclarationModule1\Setup;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        //Create first table
        $table = $installer->getConnection()
            ->newTable($installer->getTable('reference_table'))
            ->addColumn(
                'smallint_ref',
                Table::TYPE_SMALLINT,
                3,
                ['primary' => true, 'identity' => true, 'nullable' => false],
                'Smallint'
            )
            ->setComment('Reference table');
        $installer->getConnection()->createTable($table);

        $testTable = $installer->getConnection()->newTable('test_table')
            ->addColumn(
                'smallint',
                Table::TYPE_SMALLINT,
                2,
                ['nullable' => true, 'default' => 0],
                'Smallint'
            )
            ->addColumn(
                'bigint',
                Table::TYPE_BIGINT,
                10,
                ['nullable' => true, 'unsigned' => false, 'default' => 0],
                'Bigint'
            )
            ->addColumn(
                'float',
                Table::TYPE_FLOAT,
                null,
                ['default' => 0],
                'Float'
            )
            ->addColumn(
                'date',
                Table::TYPE_DATE,
                null,
                [],
                'Date'
            )
            ->addColumn(
                'timestamp',
                Table::TYPE_TIMESTAMP,
                null,
                ['default' => Table::TIMESTAMP_INIT_UPDATE],
                'Timestamp'
            )
            ->addColumn(
                'mediumtext',
                Table::TYPE_TEXT,
                11222222,
                [],
                'Mediumtext'
            )
            ->addColumn(
                'varchar',
                Table::TYPE_TEXT,
                254,
                ['nullable' => true],
                'Varchar'
            )
            ->addColumn(
                'boolean',
                Table::TYPE_BOOLEAN,
                1,
                [],
                'Boolean'
            )
            ->addIndex(
                $installer->getIdxName($installer->getTable('test_table'), ['smallint', 'bigint']),
                ['smallint', 'bigint'],
                ['type' => Mysql::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $installer->getIdxName($installer->getTable('test_table'), ['bigint']),
                ['bigint']
            )
            ->addForeignKey(
                $installer->getFkName(
                    $installer->getTable('test_table'),
                    'smallint',
                    $installer->getTable('reference_table'),
                    'smallint_ref'
                ),
                'smallint',
                $installer->getTable('reference_table'),
                'smallint_ref',
                Table::ACTION_CASCADE
            )
            ->setComment('Test Table');
        $installer->getConnection()->createTable($testTable);

        $installer->endSetup();
    }
}
