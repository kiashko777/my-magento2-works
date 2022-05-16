<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\EncryptionKey\Setup\Patch\Data;

use Magento\Config\Model\Config\Structure\Proxy;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SodiumChachaPatchTest extends TestCase
{
    const PATH_KEY = 'crypt/key';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DeploymentConfig
     */
    private $deployConfig;

    public function testChangeEncryptionKey()
    {
        $testPath = 'test/config';
        $testValue = 'test';

        $structureMock = $this->createMock(Proxy::class);
        $structureMock->expects($this->once())
            ->method('getFieldPathsByAttribute')
            ->willReturn([$testPath]);
        $structureMock->expects($this->once())
            ->method('getFieldPaths')
            ->willReturn([]);

        /** @var Config $configModel */
        $configModel = $this->objectManager->create(Config::class);
        $configModel->saveConfig($testPath, $this->legacyEncrypt($testValue), 'default', 0);

        /** @var SodiumChachaPatch $patch */
        $patch = $this->objectManager->create(
            SodiumChachaPatch::class,
            [
                'structure' => $structureMock,
            ]
        );
        $patch->apply();

        $connection = $configModel->getConnection();
        $values = $connection->fetchPairs(
            $connection->select()->from(
                $configModel->getMainTable(),
                ['config_id', 'value']
            )->where(
                'path IN (?)',
                [$testPath]
            )->where(
                'value NOT LIKE ?',
                ''
            )
        );

        /** @var EncryptorInterface $encyptor */
        $encyptor = $this->objectManager->get(EncryptorInterface::class);

        $rawConfigValue = array_pop($values);

        $this->assertNotEquals($testValue, $rawConfigValue);
        $this->assertStringStartsWith('0:' . Encryptor::CIPHER_LATEST . ':', $rawConfigValue);
        $this->assertEquals($testValue, $encyptor->decrypt($rawConfigValue));
    }

    private function legacyEncrypt(string $data): string
    {
        // @codingStandardsIgnoreStart
        $handle = @mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
        $initVectorSize = @mcrypt_enc_get_iv_size($handle);
        $initVector = str_repeat("\0", $initVectorSize);
        @mcrypt_generic_init($handle, $this->deployConfig->get(static::PATH_KEY), $initVector);

        $encrpted = @mcrypt_generic($handle, $data);

        @mcrypt_generic_deinit($handle);
        @mcrypt_module_close($handle);
        // @codingStandardsIgnoreEnd

        return '0:' . Encryptor::CIPHER_RIJNDAEL_256 . ':' . base64_encode($encrpted);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->deployConfig = $this->objectManager->get(DeploymentConfig::class);
    }
}
