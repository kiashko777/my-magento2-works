<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Framework\Interception\Config;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager\ConfigLoader\Compiled;
use Magento\Framework\App\ObjectManager\ConfigWriter\Filesystem;
use Magento\Framework\App\ObjectManager\ConfigWriterInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    const CACHE_ID = 'interceptiontest';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var ConfigWriterInterface
     */
    private $configWriter;

    /**
     * Test load interception cache from generated/metadata
     * @dataProvider interceptionCompiledConfigDataProvider
     * @param array $testConfig
     */
    public function testInstantiateFromCompiled(array $testConfig)
    {
        $this->configWriter->write(self::CACHE_ID, $testConfig);
        $config = $this->getConfig();

        $this->assertEquals($testConfig, $config->load(self::CACHE_ID));
    }

    /**
     * Create instance of Config class with specific cacheId. This is done to prevent our test
     * from altering the interception config that may have been generated during application
     * installation. Inject a new instance of the compileLoaded to bypass it's caching.
     *
     * @return CacheManager
     */
    private function getConfig()
    {
        return $this->objectManager->create(
            CacheManager::class,
            [
                'cacheId' => self::CACHE_ID,
                'compiledLoader' => $this->objectManager->create(
                    Compiled::class
                ),
            ]
        );
    }

    /**
     * Test load interception cache from backend cache
     * @dataProvider interceptionCacheConfigDataProvider
     * @param array $testConfig
     */
    public function testInstantiateFromCache(array $testConfig)
    {
        $this->cache->save($this->serializer->serialize($testConfig), self::CACHE_ID);
        $config = $this->getConfig();

        $this->assertEquals($testConfig, $config->load(self::CACHE_ID));
    }

    public function interceptionCompiledConfigDataProvider()
    {
        return [
            [['classA' => true, 'classB' => false]],
            [['classA' => false, 'classB' => true]],
        ];
    }

    public function interceptionCacheConfigDataProvider()
    {
        return [
            [['classC' => true, 'classD' => false]],
            [['classC' => false, 'classD' => true]],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->serializer = $this->objectManager->get(SerializerInterface::class);
        $this->cache = $this->objectManager->get(CacheInterface::class);
        $this->configWriter =
            $this->objectManager->get(Filesystem::class);

        $this->initializeMetadataDirectory();
    }

    /**
     * Ensure generated/metadata exists
     */
    private function initializeMetadataDirectory()
    {
        $diPath = DirectoryList::getDefaultConfig()[DirectoryList::GENERATED_METADATA][DirectoryList::PATH];
        $fullPath = BP . DIRECTORY_SEPARATOR . $diPath;
        if (!file_exists($fullPath)) {
            mkdir($fullPath);
        }
    }

    /**
     * Delete compiled file if it was created and clear cache data
     */
    protected function tearDown(): void
    {
        $compiledPath = Compiled::getFilePath(self::CACHE_ID);
        if (file_exists($compiledPath)) {
            unlink($compiledPath);
        }

        $this->cache->remove(self::CACHE_ID);
    }
}
