<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\EncryptionKey\Model\ResourceModel\Key;

use Exception;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ChangeTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     */
    public function testChangeEncryptionKeyConfigNotWritable()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Deployment configuration file is not writable');

        $writerMock = $this->createMock(Writer::class);
        $writerMock->expects($this->once())->method('checkIfWritable')->willReturn(false);

        /** @var Change $keyChangeModel */
        $keyChangeModel = $this->objectManager->create(
            Change::class,
            ['writer' => $writerMock]
        );
        $keyChangeModel->changeEncryptionKey();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/EncryptionKey/_files/payment_info.php
     */
    public function testChangeEncryptionKey()
    {
        $testPath = 'test/config';
        $testValue = 'test';

        $writerMock = $this->createMock(Writer::class);
        $writerMock->expects($this->once())->method('checkIfWritable')->willReturn(true);

        $structureMock = $this->createMock(Structure::class);
        $structureMock->expects($this->once())
            ->method('getFieldPathsByAttribute')
            ->willReturn([$testPath]);

        /** @var Change $keyChangeModel */
        $keyChangeModel = $this->objectManager->create(
            Change::class,
            ['structure' => $structureMock, 'writer' => $writerMock]
        );

        $configModel = $this->objectManager->create(
            Config::class
        );
        $configModel->saveConfig($testPath, 'test', 'default', 0);
        $this->assertNotNull($keyChangeModel->changeEncryptionKey());

        $connection = $keyChangeModel->getConnection();
        // Verify that the config value has been encrypted
        $values1 = $connection->fetchPairs(
            $connection->select()->from(
                $keyChangeModel->getTable('core_config_data'),
                ['config_id', 'value']
            )->where(
                'path IN (?)',
                [$testPath]
            )->where(
                'value NOT LIKE ?',
                ''
            )
        );
        $this->assertNotContains($testValue, $values1);
        $this->assertMatchesRegularExpression('|([0-9]+:)([0-9]+:)([a-zA-Z0-9+/]+=*)|', current($values1));

        // Verify that the credit card number has been encrypted
        $values2 = $connection->fetchPairs(
            $connection->select()->from(
                $keyChangeModel->getTable('sales_order_payment'),
                ['entity_id', 'cc_number_enc']
            )
        );
        $this->assertNotContains('1111111111', $values2);
        $this->assertMatchesRegularExpression('|([0-9]+:)([0-9]+:)([a-zA-Z0-9+/]+=*)|', current($values2));

        /** clean up */
        $select = $connection->select()->from($configModel->getMainTable())->where('path=?', $testPath);
        $this->assertNotEmpty($connection->fetchRow($select));
        $configModel->deleteConfig($testPath, 'default', 0);
        $this->assertEmpty($connection->fetchRow($select));
    }

    protected function setup(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
