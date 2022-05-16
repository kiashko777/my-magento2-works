<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Search\Model\SearchEngine;

use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Search\SearchEngine\Config\Reader;
use Magento\Search\Model\SearchEngine\Config\Data;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 *
 * @magentoAppIsolation enabled
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * Data provider for the test
     *
     * @return array
     */
    public static function loadGetDeclaredFeaturesDataProvider()
    {
        return [
            'features-synonyms' => [
                'searchEngine' => 'mysql',
                'expectedResult' => ['synonyms']
            ],
            'features-synonyms-stopwords' => [
                'searchEngine' => 'other',
                'expectedResult' => ['synonyms', 'stopwords']
            ],
            'features-none1' => [
                'searchEngine' => 'none1',
                'expectedResult' => []
            ],
            'features-none2' => [
                'searchEngine' => 'none2',
                'expectedResult' => []
            ],
            'features-none_exist' => [
                'searchEngine' => 'none_exist',
                'expectedResult' => []
            ]

        ];
    }

    /**
     * Data provider for the test
     *
     * @return array
     */
    public static function loadIsFeatureSupportedDataProvider()
    {
        return [
            [
                'feature' => 'synonyms',
                'searchEngine' => 'mysql',
                'expectedResult' => true
            ],
            [
                'feature' => 'stopwords',
                'searchEngine' => 'mysql',
                'expectedResult' => false
            ],
            [
                'feature' => 'synonyms',
                'searchEngine' => 'other',
                'expectedResult' => true
            ],
            [
                'feature' => 'stopwords',
                'searchEngine' => 'other',
                'expectedResult' => true
            ],
            [
                'feature' => 'synonyms',
                'searchEngine' => 'none1',
                'expectedResult' => false
            ],
            [
                'feature' => 'stopwords',
                'searchEngine' => 'none1',
                'expectedResult' => false
            ],
            [
                'feature' => 'synonyms',
                'searchEngine' => 'none2',
                'expectedResult' => false
            ],
            [
                'feature' => 'stopwords',
                'searchEngine' => 'none2',
                'expectedResult' => false
            ],
            [
                'feature' => 'stopwords',
                'searchEngine' => 'none_exist',
                'expectedResult' => false
            ],
            [
                'feature' => 'none_exist',
                'searchEngine' => 'none_exist',
                'expectedResult' => false
            ],
            [
                'feature' => 'none_exist',
                'searchEngine' => 'mysql',
                'expectedResult' => false
            ]
        ];
    }

    /**
     * @param string $searchEngine
     * @param array $expectedResult
     * @dataProvider loadGetDeclaredFeaturesDataProvider
     */
    public function testGetDeclaredFeatures($searchEngine, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->config->getDeclaredFeatures($searchEngine));
    }

    /**
     * @param string $searchEngine
     * @param string $feature
     * @param array $expectedResult
     * @dataProvider loadIsFeatureSupportedDataProvider
     */
    public function testIsFeatureSupported($searchEngine, $feature, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->config->isFeatureSupported($searchEngine, $feature));
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $xmlPath = __DIR__ . '/../../_files/search_engine.xml';
        $objectManager = Bootstrap::getObjectManager();

        // Clear out the cache
        $cacheManager = $objectManager->create(Manager::class);
        /** @var Manager $cacheManager */
        $cacheManager->clean($cacheManager->getAvailableTypes());

        $fileResolver = $this->getMockForAbstractClass(
            FileResolverInterface::class,
            [],
            '',
            false
        );
        $fileResolver->expects($this->any())->method('get')->willReturn([file_get_contents($xmlPath)]);

        $configReader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolver]
        );
        $dataStorage = $objectManager->create(
            Data::class,
            ['reader' => $configReader]
        );
        $this->config = $objectManager->create(
            Config::class,
            ['dataStorage' => $dataStorage]
        );
    }
}
